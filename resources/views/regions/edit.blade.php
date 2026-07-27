@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Edit Wilayah: {{ $region->name }}</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-warning">
            <form action="{{ route('regions.update', $region->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group" {{ !$user->hasRole('superadmin') ? 'hidden' : '' }}>
                        <label>Organisasi</label>
                        <select name="organization_id" class="form-control" required>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" {{ $region->organization_id == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tingkat Wilayah (Level)</label>
                        <select name="level" id="level" class="form-control" required>
                            @if(in_array('kelurahan', $allowedLevels))
                            <option value="kelurahan" {{ $region->level == 'kelurahan' ? 'selected' : '' }}>Kelurahan</option>
                            @endif
                            @if(in_array('rw', $allowedLevels))
                            <option value="rw" {{ $region->level == 'rw' ? 'selected' : '' }}>RW</option>
                            @endif
                            @if(in_array('rt', $allowedLevels))
                            <option value="rt" {{ $region->level == 'rt' ? 'selected' : '' }}>RT</option>
                            @endif
                            @if(in_array('custom', $allowedLevels))
                            <option value="custom" {{ $region->level == 'custom' ? 'selected' : '' }}>Custom</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Induk Wilayah (Parent)</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">-- Tidak Ada Induk --</option>
                            {{-- Akan diisi via JS, tapi kita pasang default jika parent sudah ada --}}
                            @if($region->parent_id)
                                <option value="{{ $region->parent_id }}" selected>
                                    {{ $region->parent->name }} ({{ $region->parent->code }})
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Wilayah</label>
                        <input type="text" name="name" class="form-control" value="{{ $region->name }}" required>
                    </div>

                    <div class="form-group">
                        <label>Kode Wilayah</label>
                        <input type="text" name="code" class="form-control" value="{{ $region->code }}">
                        <small class="text-muted">Abaikan jika tidak ingin mengubah kode yang sudah ada.</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update Data</button>
                    <a href="{{ route('regions.index') }}" class="btn btn-default">Batal</a>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
    const allRegions = @json($allRegions);
    const currentParentId = "{{ $region->parent_id }}";

    function populateParents(selectedLevel) {
        const parentSelect = document.getElementById('parent_id');
        
        let targetParentLevel = '';
        if (selectedLevel === 'rt') targetParentLevel = 'rw';
        else if (selectedLevel === 'rw') targetParentLevel = 'kelurahan';
        else if (selectedLevel === 'kelurahan') targetParentLevel = 'kecamatan';

        const filteredParents = allRegions.filter(region => region.level === targetParentLevel);
        
        // Hanya reset dropdown jika ini dipicu dari event 'change' manual oleh user
        // agar tidak menghapus selected item bawaan database saat pertama halaman diload
        parentSelect.innerHTML = '<option value="">-- Tidak Ada Induk --</option>';

        filteredParents.forEach(region => {
            const option = document.createElement('option');
            option.value = region.id;
            option.textContent = region.name + ' (' + region.code + ')';
            
            if(region.id == currentParentId) option.selected = true;
            
            parentSelect.appendChild(option);
        });
    }

    // Jalankan satu kali saat edit pertama dimuat
    populateParents(document.getElementById('level').value);

    // Event listener saat user mengganti level
    document.getElementById('level').addEventListener('change', function() {
        populateParents(this.value);
    });
</script>
@endpush
@endsection