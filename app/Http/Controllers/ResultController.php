<?php

namespace App\Http\Controllers;

use App\Models\Election;

class ResultController extends Controller
{
    public function show(Election $election)
    {
        abort_if(
            ! in_array($election->status, ['closed', 'finished']),
            403,
            'Hasil hanya bisa dilihat setelah pemungutan suara ditutup.'
        );

        $results = $election->candidates()
            ->withCount('ballots')
            ->orderByDesc('ballots_count')
            ->get();

        $totalSuara = $election->ballots()->count();
        $totalTerdaftar = $election->electionVoters()->count();
        $totalSudahMemilih = $election->electionVoters()->where('has_voted', true)->count();

        return view('results.show', compact('election', 'results', 'totalSuara', 'totalTerdaftar', 'totalSudahMemilih'));
    }
}
