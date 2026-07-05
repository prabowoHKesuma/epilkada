@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Pemilihan</h1>
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

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Daftar Agenda Pemilihan (Elections)</h3>
                <div class="card-tools">
                    <a href="{{ route('elections.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Buat Pemilihan
                    </a>
                </div>
            </div>
            
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap table-striped">
                    <thead>
                        <tr>
                            <th>Judul Pemilihan</th>
                            <th>Status</th>
                            <th>Jumlah Kandidat</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($elections as $election)
                            <tr>
                                <td><strong>{{ $election->title }}</strong></td>
                                <td>
                                    @if($election->status === 'draft')
                                        <span class="badge badge-secondary px-2 py-1">DRAFT</span>
                                    @elseif($election->status === 'open')
                                        <span class="badge badge-success px-2 py-1">OPEN / BERJALAN</span>
                                    @elseif($election->status === 'closed')
                                        <span class="badge badge-warning px-2 py-1">CLOSED / DITUTUP</span>
                                    @else
                                        <span class="badge badge-dark px-2 py-1">FINISHED / SELESAI</span>
                                    @endif
                                </td>
                                <td><span class="badge badge-info">{{ $election->candidates_count }} Kandidat</span></td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt"></i> {{ $election->start_at->format('d M Y') }} - {{ $election->end_at->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('elections.show', $election) }}" class="btn btn-xs btn-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    
                                    @if($election->status === 'draft')
                                        <a href="{{ route('elections.edit', $election) }}" class="btn btn-xs btn-info ml-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('elections.destroy', $election) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pemilihan ini? Tindakan tidak bisa dibatalkan.')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger ml-1">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada agenda pemilihan yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $elections->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection