@extends('layouts.admin') <!-- Sesuaikan dengan nama master layout AdminLTE Anda -->

@section('title', 'Verifikasi Pemilih Remote')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold"><i class="fas fa-user-check mr-2"></i>Verifikasi Pemilih Remote</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Verifikasi Remote</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="icon fas fa-check mr-2"></i> {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Daftar Antrean Pengajuan (Pending)</h3>
                <div class="card-tools">
                    <span class="badge badge-info px-3 py-1">{{ $pendings->count() }} Antrean</span>
                </div>
            </div>
            
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover table-striped align-middle text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th>Data Pemilih</th>
                            <th>Kode Verifikasi</th>
                            <th>Dokumen Bukti</th>
                            <th>Status Progres</th>
                            <th class="text-right">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendings as $rv)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $rv->voter->name ?? 'Data pemilih tidak ditemukan' }}</div>
                                    <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> Kode: {{ $rv->voter->voter_code ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-light border font-monospace px-2 py-1" style="font-size: 14px;">
                                        {{ $rv->verification_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('remote-review.document', [$rv, 'ktp']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-id-card mr-1"></i> KTP
                                        </a>
                                        <a href="{{ route('remote-review.document', [$rv, 'selfie']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-camera mr-1"></i> Selfie
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    @if($rv->verified_by_1)
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-check mr-1"></i> Tahap 1 Selesai</span>
                                        <div class="text-xs text-muted mt-1">Oleh User #{{ $rv->verified_by_1 }}</div>
                                    @else
                                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Menunggu Tahap 1</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <!-- Tombol Approval -->
                                        @if(!$rv->verified_by_1)
                                            <form action="{{ route('remote-review.approve1', $rv) }}" method="POST" class="mr-1">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-primary font-weight-bold" onclick="return confirm('Setujui verifikasi Tahap 1 untuk pemilih ini?')">
                                                    <i class="fas fa-check mr-1"></i> Setujui Tahap 1
                                                </button>
                                            </form>
                                        @elseif($rv->verified_by_1 !== auth()->id())
                                            <form action="{{ route('remote-review.approve2', $rv) }}" method="POST" class="mr-1">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success font-weight-bold" onclick="return confirm('Setujui Tahap 2 (Final) dan terbitkan token?')">
                                                    <i class="fas fa-check-double mr-1"></i> Setujui Tahap 2 (Final)
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted text-xs mr-2 font-italic">Menunggu petugas lain untuk Tahap 2</span>
                                        @endif

                                        <!-- Tombol Tolak -->
                                        <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold" data-toggle="collapse" data-target="#rejectBox-{{ $rv->id }}">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </div>

                                    <!-- Form Reject Inline -->
                                    <div class="collapse mt-2 text-left" id="rejectBox-{{ $rv->id }}">
                                        <form action="{{ route('remote-review.reject', $rv) }}" method="POST" class="form-inline justify-content-end">
                                            @csrf
                                            <div class="input-group input-group-sm w-100">
                                                <input type="text" name="reject_reason" class="form-control" placeholder="Masukkan alasan penolakan..." required>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-danger font-weight-bold">Kirim Penolakan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block text-gray-300"></i>
                                    Belum ada pengajuan verifikasi remote yang perlu diproses saat ini.
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