@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold"><i class="fas fa-ticket-alt mr-2"></i>Terbitkan Token TPS</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Token TPS</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold mt-1">Daftar Pemilihan Aktif</h3>
                <div class="card-tools">
                    <span class="badge badge-success px-3 py-2" style="font-size: 13px;">
                        <i class="fas fa-broadcast-tower mr-1"></i> {{ $elections->count() }} Pemilihan Open
                    </span>
                </div>
            </div>
            
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover table-striped align-middle text-nowrap mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 60px;" class="text-center">No.</th>
                            <th>Judul Pemilihan</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-right" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($elections as $index => $election)
                            <tr>
                                <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold text-primary" style="font-size: 16px;">
                                        {{ $election->title }}
                                    </div>
                                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Siap untuk pencetakan dan penerbitan token TPS</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-3 py-1 font-weight-normal"><i class="fas fa-lock-open mr-1"></i> Open</span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('tps-tokens.index', $election) }}" class="btn btn-sm btn-primary font-weight-bold shadow-sm px-3">
                                        <i class="fas fa-key mr-1"></i> Kelola Token
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block text-gray-300"></i>
                                    <p class="mb-1 font-weight-bold text-dark">Tidak ada pemilihan berstatus "open" saat ini.</p>
                                    <small class="text-muted">Token hanya dapat diterbitkan untuk pemilihan yang sedang aktif dibuka.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection