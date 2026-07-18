<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Ballot;
use App\Models\ElectionVoter;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\AuditLogger;

class ResultController extends Controller
{
    public function show(Election $election)
    {
        // ====================================================================
        // 1. LOGIKA PETI ES (ICE BOX) 
        // ====================================================================
        if (in_array($election->status, ['draft', 'open'])) {
            AuditLogger::log('unauthorized_result_access', "Mencoba mengintip hasil pemilihan yang masih berjalan: {$election->title}", [
                'election_id' => $election->id,
                'user_id' => auth()->id()
            ]);
            abort(403, 'PETI ES AKTIF: Hasil perolehan suara masih digembok secara sistem. Hasil baru bisa dilihat setelah pemungutan suara resmi ditutup.');
        }

        // ====================================================================
        // 2. PROSES DEKRIPSI SURAT SUARA (CRYPTOGRAPHY)
        // ====================================================================
        $privateKeyString = str_replace('\n', "\n", env('ELECTION_PRIVATE_KEY'));
        
        // Ambil data mentah tanpa agregasi database
        $candidates = $election->candidates()->orderBy('number_order', 'asc')->get();
        $ballots = $election->ballots()->get();

        $voteCounts = [];
        $totalVotes = 0; // Menghitung total suara sah yang berhasil didekripsi
        
        foreach ($ballots as $ballot) {
            $encryptedData = base64_decode($ballot->encrypted_vote);
            $decryptedCandidateId = '';
            
            // Buka gembok enkripsi menggunakan Private Key
            $success = openssl_private_decrypt($encryptedData, $decryptedCandidateId, $privateKeyString);

            if ($success) {
                if (!isset($voteCounts[$decryptedCandidateId])) {
                    $voteCounts[$decryptedCandidateId] = 0;
                }
                $voteCounts[$decryptedCandidateId]++;
                $totalVotes++; // Tambahkan ke total suara sah
            }
        }

        // Suntikkan hasil hitungan (Real Count) ke dalam object Kandidat
        foreach ($candidates as $candidate) {
            $candidate->ballots_count = $voteCounts[$candidate->id] ?? 0;
        }

        // Re-attach data kandidat yang sudah punya ballots_count ke dalam object $election
        // Ini memastikan View blade yang memanggil $election->candidates bisa membaca angkanya
        $election->setRelation('candidates', $candidates);

        // ====================================================================
        // 3. KALKULASI STATISTIK DASHBOARD
        // ====================================================================
        $totalCandidates = $candidates->count();
        $totalVoters = $election->eligibleVoters()->count(); 

        // Hitung pemilih berdasarkan kolom 'has_voted' yang akurat
        $votedCount = $election->eligibleVoters()->where('has_voted', true)->count();
        $notVotedCount = $totalVoters > $votedCount ? ($totalVoters - $votedCount) : 0;
        
        // Persentase Partisipasi
        $participationRate = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 1) : 0;

        // ====================================================================
        // 4. STATISTIK CHANNEL ALOKASI (TPS vs REMOTE)
        // ====================================================================
        $channelStats = ElectionVoter::where('election_id', $election->id)
            ->select('allowed_channel', DB::raw('count(*) as total'))
            ->groupBy('allowed_channel')
            ->get();

        // ====================================================================
        // 5. RENDER KE VIEW
        // ====================================================================
        return view('results.show', compact(
            'election', 
            'totalCandidates', 
            'totalVoters', 
            'totalVotes', 
            'votedCount', 
            'notVotedCount', 
            'participationRate',
            'channelStats'
        ));
    }
}