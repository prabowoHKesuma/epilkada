@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Pemilih</h1>
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

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Data Master Pemilih (Voters)</h3>
                <div class="card-tools">
                    @can('manage_voter')
                    <a href="{{ route('voters.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Tambah Pemilih
                    </a>
                    <a href="{{ route('voters.import.form') }}" class="btn btn-sm btn-success ml-1">
                        <i class="fas fa-file-import"></i> Import CSV
                    </a>
                    @endcan
                </div>
            </div>
            
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap table-striped">
                    <thead>
                        <tr>
                            <th>Kode Pemilih</th>
                            <th>Nama Lengkap</th>
                            <th>Alamat</th>
                            <th>Wilayah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($voters as $voter)
                            <tr>
                                <td><code>{{ $voter->voter_code }}</code></td>
                                <td>{{ $voter->name }}</td>
                                <td>{{ $voter->address ?? '-' }}</td>
                                <td>{{ $voter->region->name ?? '-' }}</td>
                                <td>
                                    @if($voter->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manage_voter')
                                        <a href="{{ route('voters.edit', $voter) }}" class="btn btn-xs btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @if($voter->is_active)
                                            <form action="{{ route('voters.destroy', $voter) }}" method="POST" class="d-inline" onsubmit="return confirm('Nonaktifkan pemilih ini?')">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger ml-1">
                                                    <i class="fas fa-ban"></i> Nonaktifkan
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-muted text-xs"><i class="fas fa-lock"></i> Read Only</span>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada data pemilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $voters->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection