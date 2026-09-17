@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Pemilihan</h1>
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
                        <h3 class="card-title">Form Edit Parameter Pemilihan</h3>
                    </div>
                    
                    <form action="{{ route('elections.update', $election) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="title">Judul Pemilihan</label>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $election->title) }}" required>
                                @error('title') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- Sisipkan Input Ini di Dalam Form (Diatas form title) --}}
                            <div class="form-group" {{ !$user->hasRole('superadmin') ? 'hidden' : '' }}>
                                <label for="organization_id">Organisasi</label>
                                <select name="organization_id" id="organization_id" class="form-control @error('organization_id') is-invalid @enderror">
                                    <option value="">-- Pilih Organisasi --</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}" @selected(old('organization_id', $election->organization_id) == $org->id)>{{ $org->name }}</option>
                                    @endforeach
                                </select>
                                @error('organization_id') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="region_id">Wilayah Pemilihan</label>
                                <select name="region_id" id="region_id" class="form-control @error('region_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                </select>
                                <small class="text-muted">Pemilihan ini hanya akan berlaku untuk wilayah yang dipilih dan turunannya.</small>
                                @error('region_id') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $election->description) }}</textarea>
                                @error('description') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="start_at">Tanggal Mulai</label>
                                <input type="datetime-local" id="start_at" name="start_at" class="form-control @error('start_at') is-invalid @enderror" value="{{ old('start_at', $election->start_at->format('Y-m-d\TH:i')) }}" required>
                                @error('start_at') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="end_at">Tanggal Selesai</label>
                                <input type="datetime-local" id="end_at" name="end_at" class="form-control @error('end_at') is-invalid @enderror" value="{{ old('end_at', $election->end_at->format('Y-m-d\TH:i')) }}" required>
                                @error('end_at') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                            <a href="{{ route('elections.index') }}" class="btn btn-default float-right">Batal</a>
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
    const oldRegionId = "{{ old('region_id', $election->region_id) }}"; // Sesuaikan ke data $election

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