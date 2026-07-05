@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Log Aktivitas Sistem</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <div class="card card-default filter-box">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Range Waktu</h3>
            </div>
            <form action="{{ request()->url() }}" method="GET">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5 col-sm-12">
                            <div class="form-group">
                                <label>Dari Tanggal & Waktu</label>
                                <input type="datetime-local" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12">
                            <div class="form-group">
                                <label>Sampai Tanggal & Waktu</label>
                                <input type="datetime-local" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search mr-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-1"></i> Jejak Audit (Audit Trail)</h3>
                
                <div class="card-tools d-flex align-items-center">
                    @if(request()->filled('start_date') || request()->filled('end_date'))
                        <a href="{{ request()->url() }}" class="btn btn-sm btn-outline-danger mr-2">
                            <i class="fas fa-undo mr-1"></i> Reset Filter
                        </a>
                    @endif
                    
                    <button onclick="window.print()" class="btn btn-sm btn-dark mr-2">
                        <i class="fas fa-print mr-1"></i> Cetak Halaman Ini
                    </button>
                    <span class="badge badge-secondary">Total Halaman Ini: {{ $logs->count() }} Data</span>
                </div>
            </div>
            
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped text-nowrap text-sm">
                    <thead>
                        <tr>
                            <th style="width: 18%">Waktu</th>
                            <th style="width: 15%">User</th>
                            <th style="width: 15%">Aksi</th>
                            <th>Deskripsi</th>
                            <th style="width: 12%">Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-clock mr-1"></i> {{ $log->created_at ? $log->created_at->format('d M Y H:i:s') : '-' }}
                                    </small>
                                </td>
                                <td>
                                    @if(isset($log->user))
                                        <span class="font-weight-bold text-primary">
                                            <i class="fas fa-user mr-1 text-xs"></i> {{ $log->user->username }}
                                        </span>
                                    @else
                                        <span class="text-muted italic"><i class="fas fa-robot mr-1 text-xs"></i> Anonim/Sistem</span>
                                    @endif
                                </td>
                                <td>
                                    <code class="text-purple font-weight-bold">{{ $log->action }}</code>
                                </td>
                                <td class="text-wrap">
                                    {{ $log->description }}
                                </td>
                                <td>
                                    <span class="badge badge-light border text-monospace">
                                        <i class="fas fa-laptop mr-1 text-xs text-muted"></i> {{ $log->ip_address }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada rekam jejak aktivitas sistem yang sesuai filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $logs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    /* Sembunyikan elemen Navigasi, Form Filter, dan Pagination saat dicetak */
    .main-header,
    .main-sidebar,
    .main-footer,
    .content-header,
    .filter-box,
    .card-tools,
    .card-footer {
        display: none !important;
    }

    .content-wrapper {
        margin-left: 0 !important;
        padding-top: 0 !important;
        background-color: #fff !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .text-wrap {
        white-space: normal !important;
    }
}
</style>
@endsection