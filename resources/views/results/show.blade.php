@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Detail Hasil Pemilihan</h1>
                <p class="text-muted mb-0">{{ $election->title }}</p>
            </div>
            <div class="col-sm-6 text-right">
                <!-- Tombol Aksi -->
                <a href="{{ route('results.print', $election) }}" target="_blank" class="btn btn-dark">
                    <i class="fas fa-print mr-1"></i> Cetak Berita Acara
                </a>
                <a href="{{ route('results.csv', $election) }}" class="btn btn-success"><i class="fas fa-file-csv mr-1"></i> Export CSV</a>
                <a href="{{ route('elections.show', $election) }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- Baris 1: Statistik Dasar -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <span class="text-muted text-sm d-block mb-1">Status</span>
                        @if($election->status === 'closed')
                            <span class="badge badge-warning px-3 py-2 text-uppercase" style="font-size: 14px;">{{ $election->status }}</span>
                        @elseif($election->status === 'finished')
                            <span class="badge badge-dark px-3 py-2 text-uppercase" style="font-size: 14px;">{{ $election->status }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <span class="text-muted text-sm d-block mb-1">Total Kandidat</span>
                        <h4 class="mb-0 font-weight-bold">{{ number_format($totalCandidates) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <span class="text-muted text-sm d-block mb-1">Total Pemilih (DPT)</span>
                        <h4 class="mb-0 font-weight-bold">{{ number_format($totalVoters) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <span class="text-muted text-sm d-block mb-1">Suara Masuk Sah</span>
                        <h4 class="mb-0 font-weight-bold">{{ number_format($totalVotes) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris 2: Analitik Partisipasi -->
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <span class="text-muted text-sm d-block mb-1">Sudah Mencoblos</span>
                        <h3 class="mb-0 text-success font-weight-bold">{{ number_format($votedCount) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <span class="text-muted text-sm d-block mb-1">Belum Mencoblos</span>
                        <h3 class="mb-0 text-secondary font-weight-bold">{{ number_format($notVotedCount) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-3 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="text-muted text-sm">Tingkat Partisipasi</span>
                            <h3 class="mb-0 font-weight-bold">{{ $participationRate }}%</h3>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $participationRate }}%" aria-valuenow="{{ $participationRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris 3: Perolehan Suara Kandidat -->
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h6 class="font-weight-bold mb-0">Perolehan Suara Kandidat</h6>
            </div>
            <div class="card-body">
                @if($election->candidates->isEmpty())
                    <div class="alert alert-info bg-light text-info border-info mb-0">
                        Belum ada kandidat yang terdaftar.
                    </div>
                @else
                    @foreach($election->candidates as $candidate)
                        @php
                            $percentage = $totalVotes > 0 ? round(($candidate->ballots_count / $totalVotes) * 100, 1) : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="font-weight-bold">No. {{ $candidate->number_order }} — {{ $candidate->name }}</span>
                                <span><strong>{{ number_format($candidate->ballots_count) }}</strong> suara</span>
                            </div>
                            <div class="progress mb-1" style="height: 12px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">{{ $percentage }}% dari total suara sah</small>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Baris 4: Breakdown Channel -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                        <h6 class="font-weight-bold mb-0">Alokasi DPT Berdasarkan Channel</h6>
                    </div>
                    <div class="card-body">
                        @if($channelStats->isEmpty())
                            <div class="alert alert-info bg-light text-info border-info mb-0">
                                Belum ada daftar pemilih yang dialokasikan.
                            </div>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($channelStats as $stat)
                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <span class="text-uppercase">{{ $stat->allowed_channel }}</span>
                                        <span class="badge badge-primary badge-pill">{{ number_format($stat->total) }} Pemilih</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection