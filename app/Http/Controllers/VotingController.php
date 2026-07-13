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

    public function submitVote(Request $request)
    {
        $request->validate(['candidate_id' => ['required', 'exists:candidates,id']]);

        $boothToken = $this->getSessionToken();
        if (! $boothToken) {
            return redirect()->route('voting.token-form')->withErrors(['token' => 'Sesi tidak valid, masukkan token lagi.']);
        }

        DB::transaction(function () use ($boothToken, $request) {
            // Kunci baris token ini -- cegah request lain memproses token yang sama secara bersamaan
            $lockedToken = TpsBoothToken::where('id', $boothToken->id)->lockForUpdate()->first();

            if (! $this->tokenIsValid($lockedToken)) {
                abort(422, 'Token sudah tidak berlaku, kemungkinan sudah dipakai di request lain.');
            }

            $candidate = Candidate::where('id', $request->candidate_id)
                ->where('election_id', $lockedToken->election_id)
                ->firstOrFail();

            Ballot::create([
                'election_id' => $lockedToken->election_id,
                'candidate_id' => $candidate->id,
                'ballot_code' => Str::random(32),
                'vote_channel' => 'tps',
            ]);
            // Perhatikan: SENGAJA tidak ada voter_id di atas -- jaga kerahasiaan suara

            $lockedToken->used_at = now();
            $lockedToken->save();

            $electionVoter = ElectionVoter::where('id', $lockedToken->election_voter_id)->lockForUpdate()->first();
            $electionVoter->has_voted = true;
            $electionVoter->voted_at = now();
            $electionVoter->save();
        });

        AuditLogger::log('cast_vote', 'Satu suara TPS tercatat.', ['election_id' => $boothToken->election_id]);

        session()->forget('voting_token_id');
        return redirect()->route('voting.thankyou');
    }

    public function thankyou()
    {
        return view('voting.thankyou');
    }

    // --- Helper privat ---

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

    public function submitVoteRemote(Request $request)
    {
        $request->validate(['candidate_id' => ['required', 'exists:candidates,id']]);

        $votingToken = $this->getSessionTokenRemote();
        if (! $votingToken) {
            return redirect()->route('voting.token-form.remote')->withErrors(['token' => 'Sesi tidak valid, masukkan token lagi.']);
        }

        DB::transaction(function () use ($votingToken, $request) {
            $lockedToken = VotingToken::where('id', $votingToken->id)->lockForUpdate()->first();

            if (! $this->tokenIsValidRemote($lockedToken)) {
                abort(422, 'Token sudah tidak berlaku, kemungkinan sudah dipakai di request lain.');
            }

            $candidate = Candidate::where('id', $request->candidate_id)
                ->where('election_id', $lockedToken->election_id)
                ->firstOrFail();

            Ballot::create([
                'election_id' => $lockedToken->election_id,
                'candidate_id' => $candidate->id,
                'ballot_code' => Str::random(32),
                'vote_channel' => 'remote',
            ]);

            $lockedToken->used_at = now();
            $lockedToken->save();

            $electionVoter = ElectionVoter::where('election_id', $lockedToken->election_id)
                ->where('voter_id', $lockedToken->voter_id)
                ->lockForUpdate()
                ->first();
            $electionVoter->has_voted = true;
            $electionVoter->voted_at = now();
            $electionVoter->save();
        });

        AuditLogger::log('cast_vote', 'Satu suara Remote tercatat.', ['election_id' => $votingToken->election_id]);

        session()->forget('remote_voting_token_id');
        return redirect()->route('voting.thankyou');
    }

}
