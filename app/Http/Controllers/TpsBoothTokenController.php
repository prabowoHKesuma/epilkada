<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\TpsBoothToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TpsBoothTokenController extends Controller
{
    public function index(Election $election)
    {
        abort_if($election->status !== 'open', 403, 'Token hanya bisa diterbitkan saat pemilihan berstatus open.');

        $eligibleVoters = $election->electionVoters()
            ->with('voter')
            ->where('has_voted', false)
            ->whereIn('allowed_channel', ['tps', 'both'])
            ->whereDoesntHave('tpsBoothToken', function ($q) {
                $q->whereNull('used_at')->whereNull('revoked_at')->where('expires_at', '>', now());
            })
            ->get();

        return view('tps-tokens.index', compact('election', 'eligibleVoters'));
    }
    public function store(Request $request, Election $election)
    {
        abort_if($election->status !== 'open', 403, 'Token hanya bisa diterbitkan saat pemilihan berstatus open.');

        $request->validate([
            'election_voter_id' => ['required', 'exists:election_voters,id'],
        ]);

        $electionVoter = ElectionVoter::findOrFail($request->election_voter_id);

        abort_if($electionVoter->election_id !== $election->id, 403);
        abort_if($electionVoter->has_voted, 403, 'Pemilih ini sudah tercatat memilih.');

        $rawToken = strtoupper(Str::random(8)); // 8 karakter, cukup pendek untuk diketik manual di bilik

        TpsBoothToken::create([
            'election_id' => $election->id,
            'election_voter_id' => $electionVoter->id,
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => now()->addMinutes(10),
            'created_by' => auth()->id(),
        ]);

        return back()->with([
            'status' => 'Token berhasil diterbitkan.',
            'generated_token' => $rawToken,
            'voter_name' => $electionVoter->voter->name,
        ]);
    }
}
