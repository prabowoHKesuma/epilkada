<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Ballot;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function show(Election $election)
    {
        // 1. Validasi Status Pemilihan
        abort_if(
            ! in_array($election->status, ['closed', 'finished']),
            403,
            'Hasil hanya bisa dilihat setelah pemungutan suara ditutup.'
        );

        // 2. Ambil Kunci Privat untuk Dekripsi
        $privateKeyString = str_replace('\n', "\n", env('ELECTION_PRIVATE_KEY'));

        // 3. Ambil data mentah dari Database
        $candidates = $election->candidates()->get();
        $ballots = $election->ballots()->get();

        // 4. Proses Dekripsi & Penghitungan Suara di Memori
        $voteCounts = [];
        
        foreach ($ballots as $ballot) {
            $encryptedData = base64_decode($ballot->encrypted_vote);
            $decryptedCandidateId = '';
            
            // Buka gembok suara satu per satu
            $success = openssl_private_decrypt($encryptedData, $decryptedCandidateId, $privateKeyString);

            if ($success) {
                if (!isset($voteCounts[$decryptedCandidateId])) {
                    $voteCounts[$decryptedCandidateId] = 0;
                }
                $voteCounts[$decryptedCandidateId]++;
            }
        }

        // 5. Suntikkan hasil hitungan (Real Count) ke dalam object Kandidat
        foreach ($candidates as $candidate) {
            // Masukkan jumlah suara ke property ballots_count (default 0 jika tidak ada suara)
            // Ini agar file View (results.show) Anda tetap berfungsi tanpa diubah
            $candidate->ballots_count = $voteCounts[$candidate->id] ?? 0;
        }

        // 6. Urutkan dari suara terbanyak ke terkecil
        $results = $candidates->sortByDesc('ballots_count')->values();

        // 7. Hitung statistik keseluruhan
        $totalSuara = $ballots->count();
        $totalTerdaftar = $election->electionVoters()->count();
        $totalSudahMemilih = $election->electionVoters()->where('has_voted', true)->count();

        // 8. Tampilkan ke Halaman
        return view('results.show', compact('election', 'results', 'totalSuara', 'totalTerdaftar', 'totalSudahMemilih'));
    }

}