@extends('layouts.admin') {{-- Sesuaikan dengan nama layout Anda --}}

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-plus-circle mr-2"></i> Tambah Role Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Manajemen Role</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        {{-- Menampilkan Error Validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">Informasi Dasar</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="label">Label Role <span class="text-danger">*</span></label>
                        <input type="text" name="label" id="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label') }}" placeholder="Contoh: Admin TPS" required>
                        <small class="text-muted">Nama tampilan role yang mudah dibaca.</small>
                    </div>
                    <div class="form-group mt-3">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Tugas dan wewenang role ini...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card card-success card-outline shadow-sm mt-4">
                <div class="card-header">
                    <h3 class="card-title">Manajemen Hak Akses (Permissions)</h3>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        @foreach($groupedPermissions as $groupName => $permissions)
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 border-0 shadow-none">
                                    <div class="card-body p-3">
                                        <h5 class="text-primary border-bottom pb-2 mb-3 font-weight-bold">{{ $groupName }}</h5>
                                        
                                        @foreach($permissions as $permission)
                                            <div class="custom-control custom-switch mb-2">
                                                <input type="checkbox" class="custom-control-input" 
                                                       name="permission_ids[]" 
                                                       value="{{ $permission->id }}" 
                                                       id="perm_{{ $permission->id }}"
                                                       {{ (is_array(old('permission_ids')) && in_array($permission->id, old('permission_ids'))) ? 'checked' : '' }}>
                                                       
                                                <label class="custom-control-label font-weight-normal" for="perm_{{ $permission->id }}" style="cursor: pointer;">
                                                    {{ $permission->label }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer text-right p-3">
                    <a href="{{ route('roles.index') }}" class="btn btn-default mr-2"><i class="fas fa-times mr-1"></i> Batal</a>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Role Baru</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection