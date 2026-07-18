<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - {{ $election->title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; line-height: 1.4; color: #333; }
        h2, h3 { margin-top: 0; margin-bottom: 5px; }
        .text-center { text-align: center; }
        .text-muted { color: #666; font-size: 9pt; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 20px; }
        
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .border-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
        }

        table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table th { background-color: #f4f4f4; }
        
        .signature-table { margin-top: 40px; text-align: center; }
        .signature-table td { width: 25%; vertical-align: top; }
        .signature-space { height: 80px; }
    </style>
</head>
<body>

    <div class="text-center mb-3">
        <h2>BERITA ACARA REKAPITULASI HASIL PEMILIHAN</h2>
        <h3>{{ $election->title ?? '-' }}</h3>
        <div class="text-muted">
            Dicetak pada: {{ $printedAt }}
        </div>
    </div>

    @if ($summary['ballot_mismatch'])
        <div class="warning">
            <strong>PERINGATAN INTEGRITAS:</strong>
            Jumlah pemilih yang tercatat sudah mencoblos adalah {{ $summary['total_voted'] }}, 
            sedangkan jumlah suara masuk sah adalah {{ $summary['total_ballots'] }}. 
            Seharusnya angka ini sama.
        </div>
    @endif

    <div class="border-box mb-3">
        <table class="info-table">
            <tr>
                <td style="width: 150px;">Nama Pemilihan</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ $election->title ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Deskripsi</td>
                <td>:</td>
                <td>{!! nl2br(e($election->description ?? '-')) !!}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td><strong>{{ strtoupper($election->status) }}</strong></td>
            </tr>
            <tr>
                <td>Waktu Mulai</td>
                <td>:</td>
                <td>{{ $election->start_at ? \Carbon\Carbon::parse($election->start_at)->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td>Waktu Selesai</td>
                <td>:</td>
                <td>{{ $election->end_at ? \Carbon\Carbon::parse($election->end_at)->format('d/m/Y H:i') : '-' }}</td>
            </tr>
        </table>
    </div>

    <h3 class="mb-2">A. Ringkasan Pemilihan</h3>
    <table class="data-table mb-3">
        <thead>
            <tr>
                <th>Uraian</th>
                <th style="width: 160px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Total Kandidat</td><td>{{ $summary['total_candidates'] }}</td></tr>
            <tr><td>Total Pemilih Terdaftar (DPT)</td><td>{{ $summary['total_voters'] }}</td></tr>
            <tr><td>Sudah Mencoblos</td><td>{{ $summary['total_voted'] }}</td></tr>
            <tr><td>Belum Mencoblos</td><td>{{ $summary['total_not_voted'] }}</td></tr>
            <tr><td>Total Suara Masuk Sah</td><td>{{ $summary['total_ballots'] }}</td></tr>
            <tr><td>Persentase Partisipasi</td><td>{{ $summary['turnout_percent'] }}%</td></tr>
            <tr>
                <td>Status Integritas Data</td>
                <td><strong>{{ $summary['integrity_label'] }}</strong></td>
            </tr>
        </tbody>
    </table>

    <h3 class="mb-2">B. Perolehan Suara Kandidat</h3>
    <table class="data-table mb-3">
        <thead>
            <tr>
                <th style="width: 80px; text-align:center;">No Urut</th>
                <th>Nama Kandidat</th>
                <th style="width: 120px; text-align:center;">Suara</th>
                <th style="width: 120px; text-align:center;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($candidates as $candidate)
                <tr>
                    <td style="text-align:center;">{{ $candidate->number_order }}</td>
                    <td><strong>{{ $candidate->name }}</strong></td>
                    <td style="text-align:center;">{{ $candidate->ballots_count }}</td>
                    <td style="text-align:center;">{{ $candidate->vote_percent }}%</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">Belum ada kandidat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="mb-2">C. Partisipasi Berdasarkan Hak Channel</h3>
    <table class="data-table mb-3">
        <thead>
            <tr>
                <th>Hak Channel</th>
                <th style="width: 120px; text-align:center;">Total Pemilih</th>
                <th style="width: 120px; text-align:center;">Sudah Coblos</th>
                <th style="width: 120px; text-align:center;">Partisipasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($channelStats as $stat)
                <tr>
                    <td style="text-transform: uppercase;">{{ $stat->allowed_channel }}</td>
                    <td style="text-align:center;">{{ $stat->total_voters }}</td>
                    <td style="text-align:center;">{{ $stat->total_voted }}</td>
                    <td style="text-align:center;">{{ $stat->turnout_percent }}%</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">Belum ada data channel.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="mb-2">D. Pernyataan</h3>
    <p>
        Dengan ini panitia menyatakan bahwa rekapitulasi hasil pemilihan di atas
        dicetak berdasarkan data yang tercatat pada sistem pemilihan elektronik dan proses dekripsi kriptografi yang sah.
    </p>
    <p>
        Berita acara ini dibuat untuk digunakan sebagaimana mestinya sebagai arsip
        dan bahan pengesahan hasil pemilihan.
    </p>

    <table class="signature-table">
        <tr>
            <td>
                Ketua Panitia
                <div class="signature-space"></div>
                ( __________________ )
            </td>
            <td>
                Saksi 1
                <div class="signature-space"></div>
                ( __________________ )
            </td>
            <td>
                Saksi 2
                <div class="signature-space"></div>
                ( __________________ )
            </td>
            <td>
                Perwakilan Warga
                <div class="signature-space"></div>
                ( __________________ )
            </td>
        </tr>
    </table>

</body>
</html>