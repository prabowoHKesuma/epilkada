@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Hasil Perolehan Suara</h1>
                <p class="text-muted">{{ $election->title }}</p>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('elections.show', $election) }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body text-center">
                        <h5 class="text-muted mb-3">Tingkat Partisipasi</h5>
                        <h2 class="display-4 font-weight-bold">
                            {{ $totalTerdaftar > 0 ? round($totalSudahMemilih / $totalTerdaftar * 100, 1) : 0 }}%
                        </h2>
                        <p class="text-muted">
                            {{ $totalSudahMemilih }} dari {{ $totalTerdaftar }} pemilih telah menggunakan hak suara.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Perolehan Suara Kandidat</h3>
                    </div>
                    <div class="card-body">
                        @foreach ($results as $candidate)
                            @php
                                $persen = $totalSuara > 0 ? round($candidate->ballots_count / $totalSuara * 100, 1) : 0;
                            @endphp
                            
                            <div class="progress-group">
                                <span class="progress-text font-weight-bold">
                                    No. {{ $candidate->number_order }} — {{ $candidate->name }}
                                </span>
                                <span class="float-right">
                                    <b>{{ $candidate->ballots_count }}</b> / {{ $totalSuara }} suara
                                </span>
                                <div class="progress progress-sm mt-1">
                                    <div class="progress-bar bg-primary" style="width: {{ $persen }}%"></div>
                                </div>
                                <small class="text-muted">{{ $persen }}% dari total suara sah</small>
                            </div>
                            
                            @if (!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    </div>
                    <div class="card-footer bg-light">
                        <div class="text-sm text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Total suara sah masuk: <strong>{{ $totalSuara }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection