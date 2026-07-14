<?php

namespace App\Http\Controllers;

use App\Models\TpsBoothToken;
use App\Models\ElectionVoter;
use App\Models\Candidate;
use App\Models\Ballot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\AuditLogger;
use App\Models\VotingToken;
use Exception;

class VotingController extends Controller
{
    public function showTokenForm()
    {
        return view('voting.token-form');
    }

    public function verifyToken(Request $request)
    {
        $request->validate(['token' => ['required', 'string']]);

        $tokenHash = hash('sha256', strtoupper(trim($request->token)));
        $boothToken = TpsBoothToken::where('token_hash', $tokenHash)->first();

        if (! $this->tokenIsValid($boothToken)) {
            return back()->withErrors(['token' => 'Token tidak valid, sudah dipakai, atau kedaluwarsa.']);
        }

        session(['voting_token_id' => $boothToken->id]);
        return redirect()->route('voting.booth');
    }

    public function showBooth()
    {
        $boothToken = $this->getSessionToken();
        if (! $boothToken) {
            return redirect()->route('voting.token-form')->withErrors(['token' => 'Sesi tidak valid, masukkan token lagi.']);
        }

        $election = $boothToken->election()->with(['candidates' => function ($q) {
            $q->where('is_active', true)->orderBy('number_order');
        }])->first();

        return view('voting.booth', compact('election'));
    }

    // ---------------------------------------------------------
    // FUNGSI SUBMIT TPS (SUDAH DIUPGRADE DENGAN ENKRIPSI & JSON)
    // ---------------------------------------------------------
    public function submitVote(Request $request)
    {
        $request->validate(['candidate_id' => ['required', 'exists:candidates,id']]);

        $boothToken = $this->getSessionToken();
        if (! $boothToken) {
            // Ubah menjadi return JSON karena ditembak via AJAX
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid, silakan refresh halaman dan masukkan token lagi.'], 401);
        }

        try {
            DB::transaction(function () use ($boothToken, $request) {
                // 1. Kunci baris token ini -- cegah request ganda secara milidetik
                $lockedToken = TpsBoothToken::where('id', $boothToken->id)->lockForUpdate()->first();

                if (! $this->tokenIsValid($lockedToken)) {
                    throw new Exception('Token sudah tidak berlaku, kemungkinan sudah terpakai.');
                }

                $candidate = Candidate::where('id', $request->candidate_id)
                    ->where('election_id', $lockedToken->election_id)
                    ->firstOrFail();

                // 2. PROSES ENKRIPSI END-TO-END
                $publicKeyString = str_replace('\n', "\n", env('ELECTION_PUBLIC_KEY'));
                $encryptedData = '';
                $isEncrypted = openssl_public_encrypt($candidate->id, $encryptedData, $publicKeyString);
                
                if (!$isEncrypted) {
                    throw new Exception('Sistem gagal mengenkripsi suara Anda.');
                }
                
                $base64EncryptedVote = base64_encode($encryptedData);

                // 3. SIMPAN KE KOTAK SUARA
                Ballot::create([
                    'election_id' => $lockedToken->election_id,
                    'ballot_code' => Str::random(32),
                    'vote_channel' => 'tps',
                    'encrypted_vote' => $base64EncryptedVote,
                    // Pastikan 'candidate_id' tidak dimasukkan lagi agar rahasia
                ]);

                // 4. HANGUSKAN TOKEN
                $lockedToken->used_at = now();
                $lockedToken->save();

                // 5. UPDATE STATUS PEMILIH
                $electionVoter = ElectionVoter::where('id', $lockedToken->election_voter_id)->lockForUpdate()->first();
                $electionVoter->has_voted = true;
                $electionVoter->voted_at = now();
                $electionVoter->save();
            });

            AuditLogger::log('cast_vote', 'Satu suara TPS tercatat (Terenkripsi).', ['election_id' => $boothToken->election_id]);
            session()->forget('voting_token_id');

            // Berhasil! Kirim respon JSON ke AJAX
            return response()->json(['success' => true, 'message' => 'Suara berhasil diamankan!']);

        } catch (Exception $e) {
            // Gagal! (Entah karena token ganda atau error enkripsi), kirim JSON error
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function thankyou()
    {
        return view('voting.thankyou');
    }

    private function tokenIsValid(?TpsBoothToken $token): bool
    {
        if (! $token) return false;
        if ($token->used_at) return false;
        if ($token->revoked_at) return false;
        if ($token->expires_at->isPast()) return false;
        if ($token->election->status !== 'open') return false;

        return true;
    }

    private function getSessionToken(): ?TpsBoothToken
    {
        $tokenId = session('voting_token_id');
        if (! $tokenId) return null;

        $token = TpsBoothToken::find($tokenId);
        return $this->tokenIsValid($token) ? $token : null;
    }

    public function showTokenFormRemote()
    {
        return view('voting.token-form-remote');
    }

    public function verifyTokenRemote(Request $request)
    {
        $request->validate(['token' => ['required', 'string']]);

        $tokenHash = hash('sha256', strtoupper(trim($request->token)));
        $votingToken = VotingToken::where('token_hash', $tokenHash)->first();

        if (! $this->tokenIsValidRemote($votingToken)) {
            return back()->withErrors(['token' => 'Token tidak valid, sudah dipakai, atau kedaluwarsa.']);
        }

        session(['remote_voting_token_id' => $votingToken->id]);
        return redirect()->route('voting.booth.remote');
    }

    private function tokenIsValidRemote(?VotingToken $token): bool
    {
        if (! $token) return false;
        if ($token->used_at) return false;
        if ($token->revoked_at) return false;
        if ($token->expires_at->isPast()) return false;
        if ($token->election->status !== 'open') return false;

        return true;
    }

    private function getSessionTokenRemote(): ?VotingToken
    {
        $tokenId = session('remote_voting_token_id');
        if (! $tokenId) return null;

        $token = VotingToken::find($tokenId);
        return $this->tokenIsValidRemote($token) ? $token : null;
    }

    public function showBoothRemote()
    {
        $votingToken = $this->getSessionTokenRemote();
        if (! $votingToken) {
            return redirect()->route('voting.token-form.remote')->withErrors(['token' => 'Sesi tidak valid, masukkan token lagi.']);
        }

        $election = $votingToken->election()->with(['candidates' => function ($q) {
            $q->where('is_active', true)->orderBy('number_order');
        }])->first();

        return view('voting.booth-remote', compact('election'));
    }

    // ---------------------------------------------------------
    // FUNGSI SUBMIT REMOTE (SUDAH DIUPGRADE DENGAN ENKRIPSI & JSON)
    // ---------------------------------------------------------
    public function submitVoteRemote(Request $request)
    {
        $request->validate(['candidate_id' => ['required', 'exists:candidates,id']]);

        $votingToken = $this->getSessionTokenRemote();
        if (! $votingToken) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid, silakan refresh halaman.'], 401);
        }

        try {
            DB::transaction(function () use ($votingToken, $request) {
                // 1. Kunci Baris Token
                $lockedToken = VotingToken::where('id', $votingToken->id)->lockForUpdate()->first();

                if (! $this->tokenIsValidRemote($lockedToken)) {
                    throw new Exception('Token sudah tidak berlaku, kemungkinan sudah terpakai.');
                }

                $candidate = Candidate::where('id', $request->candidate_id)
                    ->where('election_id', $lockedToken->election_id)
                    ->firstOrFail();

                // 2. PROSES ENKRIPSI END-TO-END
                $publicKeyString = str_replace('\n', "\n", env('ELECTION_PUBLIC_KEY'));
                $encryptedData = '';
                $isEncrypted = openssl_public_encrypt($candidate->id, $encryptedData, $publicKeyString);
                
                if (!$isEncrypted) {
                    throw new Exception('Sistem gagal mengenkripsi suara Anda.');
                }
                
                $base64EncryptedVote = base64_encode($encryptedData);

                // 3. SIMPAN KE KOTAK SUARA
                Ballot::create([
                    'election_id' => $lockedToken->election_id,
                    'ballot_code' => Str::random(32),
                    'vote_channel' => 'remote',
                    'encrypted_vote' => $base64EncryptedVote,
                ]);

                // 4. HANGUSKAN TOKEN
                $lockedToken->used_at = now();
                $lockedToken->save();

                // 5. UPDATE STATUS PEMILIH
                $electionVoter = ElectionVoter::where('election_id', $lockedToken->election_id)
                    ->where('voter_id', $lockedToken->voter_id)
                    ->lockForUpdate()
                    ->first();
                $electionVoter->has_voted = true;
                $electionVoter->voted_at = now();
                $electionVoter->save();
            });

            AuditLogger::log('cast_vote', 'Satu suara Remote tercatat (Terenkripsi).', ['election_id' => $votingToken->election_id]);
            session()->forget('remote_voting_token_id');

            return response()->json(['success' => true, 'message' => 'Suara berhasil diamankan!']);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}