@extends('layouts.admin') {{-- Sesuaikan dengan nama file layout utama Anda (misal: layouts.admin atau layouts.main) --}}

@section('content')
<form action="{{ route('roles.update', $role->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Informasi Dasar</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="label">Label Role <span class="text-danger">*</span></label>
                <input type="text" name="label" id="label" class="form-control" value="{{ old('label', $role->label) }}" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card card-success card-outline mt-4">
        <div class="card-header">
            <h3 class="card-title">Manajemen Hak Akses (Permissions)</h3>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($groupedPermissions as $groupName => $permissions)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <h5 class="text-primary border-bottom pb-2 mb-3">{{ $groupName }}</h5>
                        
                        @foreach($permissions as $permission)
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" 
                                       name="permission_ids[]" 
                                       value="{{ $permission->id }}" 
                                       id="perm_{{ $permission->id }}"
                                       {{ (is_array(old('permission_ids')) && in_array($permission->id, old('permission_ids'))) || (isset($rolePermissionIds) && in_array($permission->id, $rolePermissionIds)) ? 'checked' : '' }}>
                                       
                                <label class="custom-control-label font-weight-normal" for="perm_{{ $permission->id }}">
                                    {{ $permission->label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="{{ route('roles.index') }}" class="btn btn-default mr-2">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Role</button>
        </div>
    </div>
</form>
@endsection