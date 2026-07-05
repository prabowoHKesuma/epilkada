@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tambah Kandidat</h1>
                <p class="text-sm text-muted mt-1">Agenda: {{ $election->title }}</p>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('elections.show', $election) }}" class="btn btn-default float-sm-right btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail
                </a>
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
                        <h3 class="card-title">Form Registrasi Kandidat Baru</h3>
                    </div>
                    
                    <form action="{{ route('candidates.store', $election) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="number_order">Nomor Urut Pasangan / Kandidat</label>
                                <input type="number" id="number_order" name="number_order" class="form-control @error('number_order') is-invalid @enderror" value="{{ old('number_order') }}" placeholder="Contoh: 1" required>
                                @error('number_order') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="name">Nama Lengkap Kandidat</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama kandidat atau pasangan" required>
                                @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="photo">Foto Resmi Kandidat</label>
                                <div class="input-group">
                                    <input type="file" id="photo" name="photo" class="form-control-file @error('photo') is-invalid @enderror" accept="image/*">
                                </div>
                                @error('photo') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="vision">Visi</label>
                                <textarea id="vision" name="vision" class="form-control @error('vision') is-invalid @enderror" rows="3" placeholder="Tuliskan visi kandidat di sini...">{{ old('vision') }}</textarea>
                                @error('vision') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="mission">Misi</label>
                                <textarea id="mission" name="mission" class="form-control @error('mission') is-invalid @enderror" rows="4" placeholder="Tuliskan poin-poin misi kandidat di sini...">{{ old('mission') }}</textarea>
                                @error('mission') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan Pasangan Kandidat</button>
                            <a href="{{ route('elections.show', $election) }}" class="btn btn-default float-right">Batal</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection