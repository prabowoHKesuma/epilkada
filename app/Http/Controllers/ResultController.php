<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Ballot;
use App\Models\ElectionVoter;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use \Illuminate\Support\Str;

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

    public function print(Election $election)
    {
        // 1. LOGIKA PETI ES
        if (in_array($election->status, ['draft', 'open'])) {
            abort(403, 'Hasil perolehan suara masih digembok secara sistem.');
        }

        // 2. PROSES DEKRIPSI SURAT SUARA
        $privateKeyString = str_replace('\n', "\n", env('ELECTION_PRIVATE_KEY'));
        $candidates = $election->candidates()->orderBy('number_order', 'asc')->get();
        $ballots = $election->ballots()->get();

        $voteCounts = [];
        $totalVotes = 0;
        
        foreach ($ballots as $ballot) {
            $encryptedData = base64_decode($ballot->encrypted_vote);
            $decryptedCandidateId = '';
            $success = openssl_private_decrypt($encryptedData, $decryptedCandidateId, $privateKeyString);

            if ($success) {
                if (!isset($voteCounts[$decryptedCandidateId])) {
                    $voteCounts[$decryptedCandidateId] = 0;
                }
                $voteCounts[$decryptedCandidateId]++;
                $totalVotes++;
            }
        }

        foreach ($candidates as $candidate) {
            $candidate->ballots_count = $voteCounts[$candidate->id] ?? 0;
            $candidate->vote_percent = $totalVotes > 0 ? round(($candidate->ballots_count / $totalVotes) * 100, 2) : 0;
        }

        // 3. KALKULASI STATISTIK
        $totalCandidates = $candidates->count();
        $totalVoters = $election->eligibleVoters()->count();
        $votedCount = $election->eligibleVoters()->where('has_voted', true)->count();
        $notVotedCount = $totalVoters > $votedCount ? ($totalVoters - $votedCount) : 0;
        $participationRate = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 2) : 0;

        // Peringatan Integritas Data (Mencocokkan jumlah pemilih dgn jumlah surat suara)
        $ballotMismatch = $votedCount !== $totalVotes;
        $integrityLabel = $ballotMismatch ? 'TIDAK VALID (TERDAPAT SELISIH)' : 'OK (VALID)';

        $summary = [
            'total_candidates' => $totalCandidates,
            'total_voters' => $totalVoters,
            'total_voted' => $votedCount,
            'total_not_voted' => $notVotedCount,
            'total_ballots' => $totalVotes,
            'turnout_percent' => $participationRate,
            'ballot_mismatch' => $ballotMismatch,
            'integrity_label' => $integrityLabel,
        ];

        // 4. STATISTIK CHANNEL
        $channelStats = ElectionVoter::where('election_id', $election->id)
            ->select('allowed_channel', DB::raw('count(*) as total_voters'), DB::raw('SUM(has_voted = 1) as total_voted'))
            ->groupBy('allowed_channel')
            ->get()
            ->map(function($stat) {
                $stat->turnout_percent = $stat->total_voters > 0 ? round(($stat->total_voted / $stat->total_voters) * 100, 2) : 0;
                return $stat;
            });

        // 5. RENDER KE PDF
        $pdf = Pdf::loadView('results.pdf', [
            'election' => $election,
            'candidates' => $candidates,
            'summary' => $summary,
            'channelStats' => $channelStats,
            'printedAt' => now()->format('d/m/Y H:i:s')
        ])->setPaper('folio', 'portrait'); // <-- Tambahkan pengaturan kertas di sini

        // Menggunakan stream agar tampil di browser
        return $pdf->stream('Berita_Acara_Hasil_Pemilihan_' . Str::slug($election->title) . '.pdf');
    }

    public function exportCsv(Election $election)
    {
        // 1. LOGIKA PETI ES
        if (in_array($election->status, ['draft', 'open'])) {
            abort(403, 'Hasil perolehan suara masih digembok secara sistem.');
        }

        // 2. PROSES DEKRIPSI SURAT SUARA (Sama seperti sebelumnya)
        $privateKeyString = str_replace('\n', "\n", env('ELECTION_PRIVATE_KEY'));
        $candidates = $election->candidates()->orderBy('number_order', 'asc')->get();
        $ballots = $election->ballots()->get();

        $voteCounts = [];
        $totalVotes = 0;
        
        foreach ($ballots as $ballot) {
            $encryptedData = base64_decode($ballot->encrypted_vote);
            $decryptedCandidateId = '';
            $success = openssl_private_decrypt($encryptedData, $decryptedCandidateId, $privateKeyString);

            if ($success) {
                if (!isset($voteCounts[$decryptedCandidateId])) {
                    $voteCounts[$decryptedCandidateId] = 0;
                }
                $voteCounts[$decryptedCandidateId]++;
                $totalVotes++;
            }
        }

        // 3. SIAPKAN NAMA FILE & HEADER HTTP
        $fileName = 'Hasil_Pemilihan_' . \Illuminate\Support\Str::slug($election->title) . '_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 4. GENERATE DATA CSV VIA STREAM (Performa Cepat & Hemat Memori)
        $callback = function() use ($election, $candidates, $voteCounts, $totalVotes) {
            $file = fopen('php://output', 'w');

            // WAJIB: Tambahkan BOM (Byte Order Mark) UTF-8 agar file bisa dibaca rapi oleh Microsoft Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Baris Judul & Info Pemilihan
            fputcsv($file, ['BERITA ACARA HASIL PEMILIHAN']);
            fputcsv($file, ['Nama Pemilihan', $election->title]);
            fputcsv($file, ['Status', strtoupper($election->status)]);
            fputcsv($file, ['Dicetak Pada', now()->format('d/m/Y H:i:s')]);
            fputcsv($file, []); // Baris Kosong

            // Bagian Tabel Kandidat
            fputcsv($file, ['No Urut', 'Nama Kandidat', 'Perolehan Suara Sah', 'Persentase (%)']);

            foreach ($candidates as $candidate) {
                $suara = $voteCounts[$candidate->id] ?? 0;
                $persentase = $totalVotes > 0 ? round(($suara / $totalVotes) * 100, 2) : 0;
                
                fputcsv($file, [
                    $candidate->number_order,
                    $candidate->name,
                    $suara,
                    $persentase
                ]);
            }

            fputcsv($file, []); // Baris Kosong

            // Bagian Ringkasan Statistik
            $totalVoters = $election->eligibleVoters()->count();
            $votedCount = $election->eligibleVoters()->where('has_voted', true)->count();
            $participationRate = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 2) : 0;

            fputcsv($file, ['RINGKASAN STATISTIK PARTISIPASI']);
            fputcsv($file, ['Total Pemilih Terdaftar (DPT)', $totalVoters]);
            fputcsv($file, ['Sudah Mencoblos', $votedCount]);
            fputcsv($file, ['Total Suara Masuk Sah', $totalVotes]);
            fputcsv($file, ['Tingkat Partisipasi (%)', $participationRate]);
            
            // Cek Integritas
            $integrityLabel = ($votedCount !== $totalVotes) ? 'TIDAK VALID (TERDAPAT SELISIH)' : 'OK (VALID)';
            fputcsv($file, ['Status Integritas Data', $integrityLabel]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}