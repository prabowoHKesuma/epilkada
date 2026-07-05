@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Pemilihan</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit Parameter Pemilihan</h3>
                    </div>
                    
                    <form action="{{ route('elections.update', $election) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="title">Judul Pemilihan</label>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $election->title) }}" required>
                                @error('title') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $election->description) }}</textarea>
                                @error('description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="start_at">Tanggal Mulai</label>
                                <input type="datetime-local" id="start_at" name="start_at" class="form-control @error('start_at') is-invalid @enderror" value="{{ old('start_at', $election->start_at->format('Y-m-d\TH:i')) }}" required>
                                @error('start_at') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="end_at">Tanggal Selesai</label>
                                <input type="datetime-local" id="end_at" name="end_at" class="form-control @error('end_at') is-invalid @enderror" value="{{ old('end_at', $election->end_at->format('Y-m-d\TH:i')) }}" required>
                                @error('end_at') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                            <a href="{{ route('elections.index') }}" class="btn btn-default float-right">Batal</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection