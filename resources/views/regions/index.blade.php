@extends('layouts.admin') {{-- Sesuaikan dengan layout AdminLTE Anda --}}

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Master Data Wilayah</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <a href="{{ route('regions.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Wilayah
                </a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped" id="regionTable">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Wilayah</th>
                            <th>Level</th>
                            <th>Induk (Parent)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regions as $region)
                        <tr>
                            <td><span class="badge badge-info">{{ $region->code }}</span></td>
                            <td>{{ $region->name }}</td>
                            <td>{{ strtoupper($region->level) }}</td>
                            <td>
                                {{ $region->parent ? $region->parent->name : '-' }}
                            </td>
                            <td>
                                <a href="{{ route('regions.edit', $region->id) }}" class="btn btn-warning btn-xs">Edit</a>
                                <form action="{{ route('regions.destroy', $region->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus wilayah ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection