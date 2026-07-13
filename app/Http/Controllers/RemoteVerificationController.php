<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\RemoteVerification;
use App\Models\VotingToken;
use App\Services\PiiHasher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RemoteVerificationController extends Controller
{
    public function form(Election $election)
    {
        abort_if($election->status !== 'open', 403, 'Pemilihan ini sedang tidak menerima pengajuan verifikasi.');
        return view('remote-verifications.form', compact('election'));
    }

    public function submit(Request $request, Election $election)
    {
        abort_if($election->status !== 'open', 403, 'Pemilihan ini sedang tidak menerima pengajuan verifikasi.');

        $validated = $request->validate([
            'voter_code' => ['required', 'string'],
            'nik' => ['required', 'digits:16'],
            'ktp_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'selfie_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'consent_accepted' => ['required', 'accepted'],
        ]);

        // Cocokkan voter_code + NIK untuk konfirmasi identitas
        $voter = Voter::where('voter_code', $validated['voter_code'])->first();
        $nikHash = PiiHasher::hash($validated['nik']);

        if (! $voter || $voter->nik_hash !== $nikHash) {
            return back()->withErrors(['voter_code' => 'Kode pemilih atau NIK tidak cocok dengan data terdaftar.']);
        }

        $electionVoter = ElectionVoter::where('election_id', $election->id)
            ->where('voter_id', $voter->id)
            ->whereIn('allowed_channel', ['remote', 'both'])
            ->first();

        if (! $electionVoter) {
            return back()->withErrors(['voter_code' => 'Anda tidak terdaftar untuk memilih lewat jalur remote di pemilihan ini.']);
        }

        if ($electionVoter->has_voted) {
            return back()->withErrors(['voter_code' => 'Anda sudah tercatat memilih sebelumnya.']);
        }

        if (RemoteVerification::where('election_id', $election->id)->where('voter_id', $voter->id)->where('status', 'pending')->exists()) {
            return back()->withErrors(['voter_code' => 'Anda sudah punya pengajuan verifikasi yang masih diproses.']);
        }

        $ktpPath = $request->file('ktp_photo')->store('verifications/ktp', 'local');
        $selfiePath = $request->file('selfie_photo')->store('verifications/selfie', 'local');

        $verificationCode = strtoupper(Str::random(8));

        RemoteVerification::create([
            'election_id' => $election->id,
            'voter_id' => $voter->id,
            'verification_code' => $verificationCode,
            'ktp_photo_path' => $ktpPath,
            'selfie_photo_path' => $selfiePath,
            'consent_accepted' => true,
            'consent_at' => now(),
            'status' => 'pending',
            'expires_at' => now()->addDays(3),
        ]);

        return view('remote-verifications.submitted', compact('verificationCode'));
    }

    public function checkStatusForm()
    {
        return view('remote-verifications.check-status');
    }

    public function checkStatus(Request $request)
    {
        $request->validate(['verification_code' => ['required', 'string']]);

        $rv = RemoteVerification::where('verification_code', strtoupper($request->verification_code))->first();

        if (! $rv) {
            return back()->withErrors(['verification_code' => 'Kode verifikasi tidak ditemukan.']);
        }

        $token = null;
        if ($rv->status === 'approved') {
            $votingToken = VotingToken::where('remote_verification_id', $rv->id)
                ->whereNull('used_at')->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->first();

            if ($votingToken && $votingToken->raw_token_temp) {
                $token = $votingToken->raw_token_temp;

                // Reveal sekali saja -- setelah ditampilkan, hapus dari database
                $votingToken->raw_token_temp = null;
                $votingToken->save();
            }
        }

        return view('remote-verifications.status-result', compact('rv', 'token'));
    }

    public function formByInvitation(string $invitationToken)
    {
        $ev = ElectionVoter::where('invitation_token', $invitationToken)->with('voter', 'election')->firstOrFail();

        abort_if($ev->election->status !== 'open', 403, 'Pemilihan ini sedang tidak menerima pengajuan verifikasi.');
        abort_if(! in_array($ev->allowed_channel, ['remote', 'both']), 403, 'Link ini tidak berlaku untuk jalur remote.');
        abort_if($ev->has_voted, 403, 'Anda sudah tercatat memilih.');

        return view('remote-verifications.form-invitation', [
            'election' => $ev->election,
            'invitationToken' => $invitationToken,
            'voterNameMasked' => substr($ev->voter->name, 0, 2).str_repeat('*', max(strlen($ev->voter->name) - 2, 0)),
        ]);
    }

    public function submitByInvitation(Request $request, string $invitationToken)
    {
        $ev = ElectionVoter::where('invitation_token', $invitationToken)->with('voter', 'election')->firstOrFail();

        abort_if($ev->election->status !== 'open', 403);
        abort_if(! in_array($ev->allowed_channel, ['remote', 'both']), 403);
        abort_if($ev->has_voted, 403);

        $validated = $request->validate([
            'nik' => ['required', 'digits:16'],
            'ktp_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'selfie_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'consent_accepted' => ['required', 'accepted'],
        ]);

        $nikHash = PiiHasher::hash($validated['nik']);
        if ($ev->voter->nik_hash !== $nikHash) {
            return back()->withErrors(['nik' => 'NIK tidak cocok dengan data terdaftar.']);
        }

        if (RemoteVerification::where('election_id', $ev->election_id)->where('voter_id', $ev->voter_id)->where('status', 'pending')->exists()) {
            return back()->withErrors(['nik' => 'Anda sudah punya pengajuan yang masih diproses.']);
        }

        $ktpPath = $request->file('ktp_photo')->store('verifications/ktp', 'local');
        $selfiePath = $request->file('selfie_photo')->store('verifications/selfie', 'local');
        $verificationCode = strtoupper(\Illuminate\Support\Str::random(8));

        RemoteVerification::create([
            'election_id' => $ev->election_id,
            'voter_id' => $ev->voter_id,
            'verification_code' => $verificationCode,
            'ktp_photo_path' => $ktpPath,
            'selfie_photo_path' => $selfiePath,
            'consent_accepted' => true,
            'consent_at' => now(),
            'status' => 'pending',
            'expires_at' => now()->addDays(3),
        ]);

        return view('remote-verifications.submitted', compact('verificationCode'));
    }
}
