@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Petugas</h1>
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
                        <h3 class="card-title">Ubah Data Petugas: {{ $user->name }}</h3>
                    </div>

                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">

                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="username">Username <small class="text-muted">(Tidak dapat diubah)</small></label>
                                <input type="text" id="username" name="username" class="form-control bg-light" value="{{ $user->username }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="password">Password Baru <small class="text-info">*Kosongkan jika tidak ingin mengubah password</small></label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Ketik jika ingin ganti password...">
                                @error('password') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group" {{ !$user->hasRole('superadmin') ? 'hidden' : '' }}>
                                <label for="organization_id">Organisasi</label>
                                <select name="organization_id" id="organization_id" class="form-control @error('organization_id') is-invalid @enderror">
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}" @selected(old('organization_id', $user->organization_id) == $org->id)>{{ $org->name }}</option>
                                    @endforeach
                                </select>
                                @error('organization_id') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="region_id">Wilayah Penugasan</label>
                                <select name="region_id" id="region_id" class="form-control @error('region_id') is-invalid @enderror">
                                    {{-- @foreach ($regions as $region)
                                        <option value="{{ $region->id }}" @selected(old('region_id', $user->region_id) == $region->id)>
                                            {{ str_repeat('— ', $region->level == 'rt' ? 2 : ($region->level == 'rw' ? 1 : 0)) }}{{ $region->name }} ({{ $region->level }})
                                        </option>
                                    @endforeach --}}
                                </select>
                                @error('region_id') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="role">Hak Akses (Role)</label>
                                <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->name }}" @selected(old('role', $user->roles->first()->name ?? '') == $r->name)>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-user-check"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-default float-right">Batal</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Ambil data wilayah berformat JSON dari Controller
    const allRegions = @json($regions);
    const orgSelect = document.getElementById('organization_id');
    const regionSelect = document.getElementById('region_id');
    const oldRegionId = "{{ old('region_id', $user->region_id) }}";

    function updateRegions() {
        const selectedOrg = orgSelect.value;
        
        // Kosongkan selalu opsi wilayah sebelum memuat yang baru
        regionSelect.innerHTML = '<option value="">-- Pilih Wilayah --</option>';

        // Jika organisasi belum dipilih, hentikan fungsi (wilayah tetap kosong)
        if (!selectedOrg) return;

        // Saring wilayah yang organization_id-nya sama dengan yang dipilih user
        const filteredRegions = allRegions.filter(r => r.organization_id == selectedOrg);
        
        filteredRegions.forEach(r => {
            const option = document.createElement('option');
            option.value = r.id;
            
            // Tambahkan indentasi visual berdasarkan level
            let prefix = '';
            if(r.level === 'rt') prefix = '— — ';
            else if(r.level === 'rw') prefix = '— ';
            
            option.textContent = prefix + r.name + ' (' + r.level.toUpperCase() + ')';
            
            // Pilih kembali opsi sebelumnya jika terjadi error validasi
            if (r.id == oldRegionId) {
                option.selected = true;
            }
            
            regionSelect.appendChild(option);
        });
    }

    // Dengarkan setiap perubahan klik pada kotak Organisasi
    orgSelect.addEventListener('change', updateRegions);
    
    // Jalankan satu kali saat halaman pertama kali dimuat
    updateRegions();
</script>
@endpush
@endsection