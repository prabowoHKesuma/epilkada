@extends('layouts.admin') {{-- Sesuaikan dengan nama file layout utama Anda (misal: layouts.admin atau layouts.main) --}}

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-user-shield mr-2"></i> Manajemen Role</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        {{-- Menampilkan Pesan Sukses / Error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                {{ session('error') }}
            </div>
        @endif

        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title mt-1">Daftar Hak Akses Sistem</h3>
                <div class="card-tools">
                    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm font-weight-bold">
                        <i class="fas fa-plus mr-1"></i> Tambah Role Baru
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover table-striped text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Nama Role</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Total Pengguna</th>
                            <th class="text-center">Total Akses</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $index => $role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $role->label }}</strong>
                                    @if($role->is_system)
                                        <span class="badge badge-danger ml-2" title="Role inti sistem tidak bisa dihapus"><i class="fas fa-lock"></i> System</span>
                                    @endif
                                    <br>
                                    <small class="text-muted text-xs">ID Spatie: {{ $role->name }}</small>
                                </td>
                                <td>{{ $role->description ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info px-2 py-1">{{ $role->users_count }} Pengguna</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-2 py-1">{{ $role->permissions_count }} Izin</span>
                                </td>
                                <td class="text-center">
                                    @if(!$role->is_system)
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning shadow-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role {{ $role->label }}? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled title="Role sistem dikunci">
                                            <i class="fas fa-ban"></i> Terkunci
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                                    Belum ada data role yang dibuat.
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