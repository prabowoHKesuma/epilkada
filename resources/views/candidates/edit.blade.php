@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Kandidat</h1>
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
                
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Form Pembaruan Data Kandidat</h3>
                    </div>
                    
                    <form action="{{ route('candidates.update', [$election, $candidate]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="number_order">Nomor Urut</label>
                                <input type="number" id="number_order" name="number_order" class="form-control @error('number_order') is-invalid @enderror" value="{{ old('number_order', $candidate->number_order) }}" required>
                                @error('number_order') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="name">Nama Kandidat</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $candidate->name) }}" required>
                                @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="photo">Foto Kandidat <span class="text-muted font-weight-normal">(Kosongkan jika tidak ingin diganti)</span></label>
                                
                                @if($candidate->photo)
                                    <div class="mb-3 d-block">
                                        <p class="text-xs text-muted mb-1">Foto saat ini:</p>
                                        <img src="{{ Storage::url($candidate->photo) }}" class="img-thumbnail" style="height: 120px; width: 120px; object-fit: cover;">
                                    </div>
                                @endif

                                <div class="input-group">
                                    <input type="file" id="photo" name="photo" class="form-control-file @error('photo') is-invalid @enderror" accept="image/*">
                                </div>
                                @error('photo') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="vision">Visi</label>
                                <textarea id="vision" name="vision" class="form-control @error('vision') is-invalid @enderror" rows="3">{{ old('vision', $candidate->vision) }}</textarea>
                                @error('vision') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="mission">Misi</label>
                                <textarea id="mission" name="mission" class="form-control @error('mission') is-invalid @enderror" rows="4">{{ old('mission', $candidate->mission) }}</textarea>
                                @error('mission') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                            <a href="{{ route('elections.show', $election) }}" class="btn btn-default float-right">Batal</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection