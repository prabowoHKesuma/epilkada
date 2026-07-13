<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemoteVerification;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\VotingToken;
use Illuminate\Support\Str;

class RemoteVerificationReviewController extends Controller
{
    public function index()
    {
        $pendings = RemoteVerification::with('voter', 'election')->where('status', 'pending')->latest()->get();
        return view('remote-verifications.review-index', compact('pendings'));
    }

    public function viewDocument(RemoteVerification $rv, string $type)
    {
        abort_unless(in_array($type, ['ktp', 'selfie']), 404);

        $path = $type === 'ktp' ? $rv->ktp_photo_path : $rv->selfie_photo_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        AuditLogger::log('view_verification_document', "Melihat dokumen $type milik pemilih {$rv->voter->voter_code}", ['election_id' => $rv->election_id]);

        return Storage::disk('local')->response($path);
    }

    public function approveStep1(RemoteVerification $rv)
    {
        abort_if($rv->status !== 'pending', 403);
        abort_if($rv->verified_by_1, 403, 'Tahap 1 sudah diverifikasi sebelumnya.');

        $rv->verified_by_1 = auth()->id();
        $rv->save();

        AuditLogger::log('remote_verify_step1', "Verifikasi tahap 1 disetujui untuk ".($rv->voter->voter_code ?? '(voter tidak ditemukan)'), ['election_id' => $rv->election_id]);

        return back()->with('status', 'Verifikasi tahap 1 dicatat. Menunggu petugas lain untuk tahap 2.');
    }

    public function approveStep2(RemoteVerification $rv)
    {
        abort_if($rv->status !== 'pending', 403);
        abort_unless($rv->verified_by_1, 403, 'Tahap 1 belum diverifikasi.');
        abort_if($rv->verified_by_1 === auth()->id(), 403, 'Verifikasi tahap 2 harus dilakukan petugas yang berbeda dari tahap 1.');

        DB::transaction(function () use ($rv) {
            $rv->verified_by_2 = auth()->id();
            $rv->status = 'approved';
            $rv->verified_at = now();
            $rv->save();

            $this->issueRemoteToken($rv);
        });

        AuditLogger::log('remote_verify_step2', "Verifikasi tahap 2 disetujui untuk ".($rv->voter->voter_code ?? '(voter tidak ditemukan)').". Token remote diterbitkan.", ['election_id' => $rv->election_id]);

        return back()->with('status', 'Verifikasi disetujui, token remote diterbitkan untuk pemilih.');
    }

    public function reject(Request $request, RemoteVerification $rv)
    {
        $request->validate(['reject_reason' => ['required', 'string']]);

        $rv->status = 'rejected';
        $rv->reject_reason = $request->reject_reason;
        $rv->save();

        AuditLogger::log('remote_verify_reject', "Verifikasi ditolak untuk {$rv->voter->voter_code}: {$request->reject_reason}", ['election_id' => $rv->election_id]);

        return back()->with('status', 'Verifikasi ditolak.');
    }

    private function issueRemoteToken(RemoteVerification $rv)
    {
        $rawToken = strtoupper(Str::random(10));

        VotingToken::create([
            'election_id' => $rv->election_id,
            'voter_id' => $rv->voter_id,
            'remote_verification_id' => $rv->id,
            'token_hash' => hash('sha256', $rawToken),
            'raw_token_temp' => $rawToken,
            'expires_at' => now()->addDays(2),
        ]);
    }
}
