@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Buat Pemilihan Baru</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Parameter Pemilihan Baru</h3>
                    </div>
                    
                    <form action="{{ route('elections.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="title">Judul Pemilihan</label>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                @error('title') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi / Keterangan Tambahan</label>
                                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                @error('description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="start_at">Tanggal & Waktu Mulai</label>
                                <input type="datetime-local" id="start_at" name="start_at" class="form-control @error('start_at') is-invalid @enderror" value="{{ old('start_at') }}" required>
                                @error('start_at') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="end_at">Tanggal & Waktu Selesai</label>
                                <input type="datetime-local" id="end_at" name="end_at" class="form-control @error('end_at') is-invalid @enderror" value="{{ old('end_at') }}" required>
                                @error('end_at') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan Agenda</button>
                            <a href="{{ route('elections.index') }}" class="btn btn-default float-right">Batal</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection