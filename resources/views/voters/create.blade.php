@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tambah Pemilih</h1>
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
                        <h3 class="card-title">Form Input Pemilih Baru</h3>
                    </div>
                    
                    <form action="{{ route('voters.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="nik">NIK (16 digit)</label>
                                <input type="text" id="nik" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" maxlength="16" required>
                                @error('nik') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="kk">Nomor KK (16 digit)</label>
                                <input type="text" id="kk" name="kk" class="form-control @error('kk') is-invalid @enderror" value="{{ old('kk') }}" maxlength="16" required>
                                @error('kk') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="address">Alamat</label>
                                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                                @error('address') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="rt">RT</label>
                                        <input type="text" id="rt" name="rt" class="form-control" value="{{ old('rt') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="rw">RW</label>
                                        <input type="text" id="rw" name="rw" class="form-control" value="{{ old('rw') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone">No. Telepon</label>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                @error('phone') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Sisipkan Input Ini di Dalam Form --}}
                            <div class="form-group" {{ !$user->hasRole('superadmin') ? 'hidden' : '' }}>
                                <label for="organization_id">Organisasi</label>
                                <select name="organization_id" id="organization_id" class="form-control @error('organization_id') is-invalid @enderror">
                                    <option value="">-- Pilih Organisasi --</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}" @selected(old('organization_id', $user->organization_id) == $org->id)>{{ $org->name }}</option>
                                    @endforeach
                                </select>
                                @error('organization_id') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="region_id">Wilayah Pemilih</label>
                                <select name="region_id" id="region_id" class="form-control @error('region_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                </select>
                                @error('region_id') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                            <a href="{{ route('voters.index') }}" class="btn btn-default float-right">Batal</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const allRegions = @json($regions);
    const orgSelect = document.getElementById('organization_id');
    const regionSelect = document.getElementById('region_id');
    const oldRegionId = "{{ old('region_id') }}"; 

    function updateRegions() {
        const selectedOrg = orgSelect.value;
        regionSelect.innerHTML = '<option value="">-- Pilih Wilayah --</option>';
        if (!selectedOrg) return;

        const filteredRegions = allRegions.filter(r => r.organization_id == selectedOrg);
        const filteredRegionIds = filteredRegions.map(r => r.id);
        const rootNodes = filteredRegions.filter(r => !filteredRegionIds.includes(r.parent_id));

        function appendChildren(parentId, indent) {
            const children = filteredRegions.filter(r => r.parent_id == parentId);
            children.forEach(child => {
                const option = document.createElement('option');
                option.value = child.id;
                option.textContent = indent + child.name + ' (' + child.level.toUpperCase() + ')';
                if (child.id == oldRegionId) option.selected = true;
                regionSelect.appendChild(option);
                appendChildren(child.id, indent + '— ');
            });
        }

        rootNodes.forEach(root => {
            const option = document.createElement('option');
            option.value = root.id;
            option.textContent = root.name + ' (' + root.level.toUpperCase() + ')';
            if (root.id == oldRegionId) option.selected = true;
            regionSelect.appendChild(option);
            appendChildren(root.id, '— ');
        });
    }

    orgSelect.addEventListener('change', updateRegions);
    updateRegions();
</script>
@endpush
@endsection