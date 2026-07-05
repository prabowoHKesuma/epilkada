@extends('layouts.admin')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tambah Menu Baru</h1>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Kita batasi lebar form agar tidak terlalu memanjang ke samping di layar besar -->
            <div class="col-md-8 col-lg-6">
                
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Data Menu</h3>
                    </div>
                    
                    <form action="{{ route('menus.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="title">Judul Menu <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Masukkan judul menu" required>
                            </div>

                            <div class="form-group">
                                <label for="menu_key">Menu Key <span class="text-danger">*</span></label>
                                <small class="d-block text-muted mb-2">Harus unik, tanpa spasi, menggunakan huruf kecil (contoh: manage_voters)</small>
                                <input type="text" name="menu_key" id="menu_key" class="form-control" value="{{ old('menu_key') }}" placeholder="contoh: manage_voters" required>
                            </div>

                            <div class="form-group">
                                <label for="url">URL Target</label>
                                <small class="d-block text-muted mb-2">Path atau rute tujuan (contoh: /voters atau menus.index)</small>
                                <input type="text" name="url" id="url" class="form-control" value="{{ old('url') }}" placeholder="contoh: /voters">
                            </div>

                            <div class="form-group">
                                <label for="icon_class">Class Ikon (Font Awesome)</label>
                                <input type="text" name="icon_class" id="icon_class" class="form-control" value="{{ old('icon_class', 'fas fa-circle') }}" placeholder="contoh: fas fa-users">
                            </div>

                            <div class="form-group">
                                <label for="parent_id">Induk Menu</label>
                                <select name="parent_id" id="parent_id" class="form-control">
                                    <option value="">-- Tidak ada (Jadikan Menu Utama) --</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sort_order">Urutan Tampil</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                            </div>

                            <div class="form-group mt-4">
                                <label>Role yang Bisa Melihat Menu Ini</label>
                                <div class="p-3 border rounded bg-light">
                                    <div class="row">
                                        @foreach ($roles as $role)
                                            <div class="col-sm-6">
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input class="custom-control-input" type="checkbox" name="role_ids[]" id="role_{{ $role->id }}" value="{{ $role->id }}">
                                                    <label for="role_{{ $role->id }}" class="custom-control-label font-weight-normal">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Card Footer berisi tombol aksi -->
                        <div class="card-footer text-right">
                            <a href="{{ route('menus.index') }}" class="btn btn-default mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Simpan Menu
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection