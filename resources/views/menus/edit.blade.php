@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Menu</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-lg-6">
                
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Form Perubahan Data Menu: <strong>{{ $menu->title }}</strong></h3>
                    </div>
                    
                    <form action="{{ route('menus.update', $menu) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="title">Judul Menu <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $menu->title) }}" required>
                                @error('title') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="menu_key">Menu Key <span class="text-danger">*</span></label>
                                <small class="d-block text-muted mb-2">Unik, tanpa spasi (contoh: manage_voters)</small>
                                <input type="text" name="menu_key" id="menu_key" class="form-control @error('menu_key') is-invalid @enderror" value="{{ old('menu_key', $menu->menu_key) }}" required>
                                @error('menu_key') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="url">URL Target</label>
                                <small class="d-block text-muted mb-2">Path atau rute tujuan (contoh: /voters)</small>
                                <input type="text" name="url" id="url" class="form-control" value="{{ old('url', $menu->url) }}">
                            </div>

                            <div class="form-group">
                                <label for="icon_class">Class Ikon (Font Awesome)</label>
                                <input type="text" name="icon_class" id="icon_class" class="form-control" value="{{ old('icon_class', $menu->icon_class) }}">
                            </div>

                            <div class="form-group">
                                <label for="parent_id">Induk Menu</label>
                                <select name="parent_id" id="parent_id" class="form-control">
                                    <option value="">-- Tidak ada (Jadikan Menu Utama) --</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" @selected(old('parent_id', $menu->parent_id) == $parent->id)>
                                            {{ $parent->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sort_order">Urutan Tampil</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $menu->sort_order) }}">
                            </div>

                            <div class="form-group mt-4">
                                <label>Role yang Bisa Melihat Menu Ini</label>
                                <div class="p-3 border rounded bg-light">
                                    <div class="row">
                                        @foreach ($roles as $role)
                                            <div class="col-sm-6">
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input class="custom-control-input" type="checkbox" name="role_ids[]" id="role_{{ $role->id }}" value="{{ $role->id }}" @checked(in_array($role->id, old('role_ids', $selectedRoleIds)))>
                                                    <label for="role_{{ $role->id }}" class="custom-control-label font-weight-normal">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1" @checked(old('is_active', $menu->is_active))>
                                    <label class="custom-control-label" for="is_active">Status Menu Aktif</label>
                                </div>
                                <small class="text-muted d-block mt-1">Jika dimatikan, menu ini tidak akan muncul di sidebar navigasi.</small>
                            </div>

                        </div>

                        <div class="card-footer text-right">
                            <a href="{{ route('menus.index') }}" class="btn btn-default mr-2">Batal</a>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection