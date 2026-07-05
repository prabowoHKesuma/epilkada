@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $election->title }}</h1>
                <p class="text-sm text-muted mt-1">
                    Status Saat Ini: 
                    @if($election->status === 'draft')
                        <span class="badge badge-secondary uppercase">{{ $election->status }}</span>
                    @elseif($election->status === 'open')
                        <span class="badge badge-success uppercase">{{ $election->status }}</span>
                    @elseif($election->status === 'closed')
                        <span class="badge badge-warning uppercase">{{ $election->status }}</span>
                    @else
                        <span class="badge badge-dark uppercase">{{ $election->status }}</span>
                    @endif
                </p>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('elections.index') }}" class="btn btn-default float-sm-right btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icon fas fa-check"></i> {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="icon fas fa-ban"></i> {{ $errors->first() }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Panel Utama Aksi Alur Pemilihan -->
        <div class="card card-outline card-secondary mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cogs mr-1"></i> Panel Kontrol Alur Pemilihan</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @if($election->status === 'draft')
                        <form action="{{ route('elections.publish', $election) }}" method="POST" class="d-inline mr-2" onsubmit="return confirm('Buka pemilihan ini? Data tidak bisa diedit lagi setelah dibuka.')">
                            @csrf @method('PATCH')
                            <button class="btn btn-success"><i class="fas fa-rocket mr-1"></i> Buka Pemilihan</button>
                        </form>
                    @elseif($election->status === 'open')
                        <form action="{{ route('elections.close', $election) }}" method="POST" class="d-inline mr-2" onsubmit="return confirm('Tutup pemungutan suara?')">
                            @csrf @method('PATCH')
                            <button class="btn btn-warning"><i class="fas fa-lock mr-1"></i> Tutup Pemungutan Suara</button>
                        </form>
                    @elseif($election->status === 'closed')
                        <form action="{{ route('elections.finish', $election) }}" method="POST" class="d-inline mr-2" onsubmit="return confirm('Selesaikan pemilihan? Hasil akan final.')">
                            @csrf @method('PATCH')
                            <button class="btn btn-dark"><i class="fas fa-flag-checkered mr-1"></i> Selesaikan & Finalisasi</button>
                        </form>
                    @endif

                    <!-- Navigasi Cepat Manajemen Konten Terkait -->
                    <div class="btn-group">
                        <a href="{{ route('candidates.create', $election) }}" class="btn btn-outline-secondary"><i class="fas fa-users-cog"></i> Kelola Kandidat</a>
                        <a href="{{ route('election-voters.index', $election) }}" class="btn btn-outline-secondary"><i class="fas fa-user-check"></i> Kelola Pemilih</a>
                        <a href="{{ route('tps-tokens.index', $election) }}" class="btn btn-outline-secondary"><i class="fas fa-key"></i> Token TPS</a>
                        @if(in_array($election->status, ['closed', 'finished']))
                            <a href="{{ route('results.show', $election) }}" class="btn btn-success"><i class="fas fa-poll"></i> Lihat Hasil Pemilihan</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Grid Monitoring Kandidat -->
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-1"></i> Daftar Pasangan Kandidat</h3>
                @if($election->status === 'draft')
                    <div class="card-tools">
                        <a href="{{ route('candidates.create', $election) }}" class="btn btn-xs btn-primary">
                            <i class="fas fa-user-plus"></i> Tambah Kandidat
                        </a>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($election->candidates as $candidate)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card card-widget widget-user-2 shadow-sm border h-100 mb-0">
                                <div class="widget-user-header bg-light d-flex flex-column align-items-center text-center p-3">
                                    @if($candidate->photo)
                                        <img src="{{ Storage::url($candidate->photo) }}" class="img-fluid rounded mb-3" style="height: 180px; width: 100%; object-fit: cover; border: 1px solid #dee2e6;">
                                    @else
                                        <div class="bg-secondary rounded mb-3 d-flex align-items-center justify-content-center" style="height: 180px; width: 100%;">
                                            <i class="fas fa-user-tie fa-4x text-light"></i>
                                        </div>
                                    @endif
                                    <h5>No Urut {{ $candidate->number_order }}</h5>
                                    <h4 class="font-weight-bold mb-0">{{ $candidate->name }}</h4>
                                </div>
                                
                                @if($election->status === 'draft')
                                    <div class="card-footer p-2 bg-white d-flex justify-content-center border-top">
                                        <a href="{{ route('candidates.edit', [$election, $candidate]) }}" class="btn btn-xs btn-info px-3">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('candidates.destroy', [$election, $candidate]) }}" method="POST" class="d-inline ml-2" onsubmit="return confirm('Hapus kandidat ini?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger px-3">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-4">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p class="mb-0">Belum ada kandidat yang didaftarkan untuk pemilihan ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection