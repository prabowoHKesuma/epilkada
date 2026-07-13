<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;
use App\Models\Voter;
use App\Models\ElectionVoter;
use App\Services\AuditLogger;

class ElectionVoterController extends Controller
{
    public function index(Election $election)
    {
        $assignedIds = $election->electionVoters()->pluck('voter_id');
        $availableVoters = Voter::where('is_active', true)->whereNotIn('id', $assignedIds)->get();
        $assignedVoters = $election->electionVoters()->with('voter')->get();

        return view('election-voters.index', compact('election', 'availableVoters', 'assignedVoters'));
    }

    public function store(Request $request, Election $election)
    {
        $validated = $request->validate([
            'voter_ids' => ['required', 'array'],
            'voter_ids.*' => ['exists:voters,id'],
            'allowed_channel' => ['required', 'in:tps,remote,both'],
        ]);

        foreach ($validated['voter_ids'] as $voterId) {
            ElectionVoter::firstOrCreate(
                ['election_id' => $election->id, 'voter_id' => $voterId],
                ['allowed_channel' => $validated['allowed_channel'], 'has_voted' => false]
            );
        }

        AuditLogger::log('election_voter_assign', "Pemilih didaftarkan ke pemilihan: {$election->title}", ['election_id' => $election->id]);
        return back()->with('status', count($validated['voter_ids']).' pemilih berhasil didaftarkan ke pemilihan ini.');
    }

    public function destroy(Election $election, ElectionVoter $electionVoter)
    {
        abort_if($electionVoter->has_voted, 403, 'Pemilih yang sudah memilih tidak bisa dihapus dari daftar.');
        abort_if($election->status !== 'draft', 403, 'Daftar pemilih hanya bisa diubah selama pemilihan masih draft.');

        $electionVoter->delete();
        AuditLogger::log('election_voter_remove', "Pemilih dikeluarkan dari pemilihan: {$election->title}", ['election_id' => $election->id]);
        return back()->with('status', 'Pemilih dikeluarkan dari daftar pemilihan ini.');
    }

    public function changeChannel(Request $request, Election $election, ElectionVoter $electionVoter)
    {
        $request->validate(['allowed_channel' => ['required', 'in:tps,remote,both']]);

        abort_if($electionVoter->has_voted, 403, 'Pemilih yang sudah memilih tidak bisa diubah jalurnya.');
        abort_if($election->status !== 'draft', 403, 'Jalur pemilih hanya bisa diubah selama pemilihan masih draft.');

        $electionVoter->allowed_channel = $request->allowed_channel;
        $electionVoter->save();

        return back()->with('status', 'Jalur pemilih diperbarui.');
    }

    
}
