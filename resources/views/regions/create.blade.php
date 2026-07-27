@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Tambah Wilayah Baru</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <form action="{{ route('regions.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group" {{ !$user->hasRole('superadmin') ? 'hidden' : '' }}>
                        <label>Organisasi</label>
                        <select name="organization_id" class="form-control" required>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tingkat Wilayah (Level)</label>
                        <select name="level" id="level" class="form-control" required>
                            <option value="">-- Pilih Level --</option>
                            @if(in_array('kelurahan', $allowedLevels))
                                <option value="kelurahan">Kelurahan</option>
                            @endif
                            @if(in_array('rw', $allowedLevels))
                                <option value="rw">RW</option>
                            @endif
                            @if(in_array('rt', $allowedLevels))
                                <option value="rt">RT</option>
                            @endif
                            @if(in_array('custom', $allowedLevels))
                                <option value="custom">Custom</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Induk Wilayah (Parent)</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">-- Tidak Ada / Pilih Level Dulu --</option>
                            {{-- Opsi ini diisi otomatis oleh JavaScript di bawah --}}
                        </select>
                        <small class="text-muted">Kosongkan jika ini adalah wilayah hierarki teratas (misal: Kelurahan).</small>
                    </div>

                    <div class="form-group">
                        <label>Nama Wilayah</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: RT 01 atau RW 004" required>
                    </div>

                    <div class="form-group">
                        <label>Kode Wilayah (Opsional)</label>
                        <input type="text" name="code" class="form-control" placeholder="Kosongkan agar digenerate otomatis oleh sistem">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('regions.index') }}" class="btn btn-default">Batal</a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Menyimpan seluruh data wilayah dari server ke dalam format JSON
    const allRegions = @json($allRegions);

    document.getElementById('level').addEventListener('change', function() {
        const selectedLevel = this.value;
        const parentSelect = document.getElementById('parent_id');
        
        // Reset dropdown
        parentSelect.innerHTML = '<option value="">-- Tidak Ada Induk --</option>';

        // Tentukan level parent yang dituju berdasarkan level yang dipilih
        let targetParentLevel = '';
        if (selectedLevel === 'rt') targetParentLevel = 'rw';
        else if (selectedLevel === 'rw') targetParentLevel = 'kelurahan';
        else if (selectedLevel === 'kelurahan') targetParentLevel = 'kecamatan';

        // Filter data wilayah berdasarkan target level
        const filteredParents = allRegions.filter(region => region.level === targetParentLevel);

        // Masukkan data hasil filter ke dalam dropdown
        filteredParents.forEach(region => {
            const option = document.createElement('option');
            option.value = region.id;
            option.textContent = region.name + ' (' + region.code + ')';
            parentSelect.appendChild(option);
        });
    });
</script>
@endpush
@endsection