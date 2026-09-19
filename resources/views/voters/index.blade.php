@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Pemilih</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icon fas fa-check"></i> {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Data Master Pemilih (Voters)</h3>
                <div class="card-tools">
                    @can('manage_voter')
                    <a href="{{ route('voters.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Tambah Pemilih
                    </a>
                    <a href="{{ route('voters.import.form') }}" class="btn btn-sm btn-success ml-1">
                        <i class="fas fa-file-import"></i> Import CSV
                    </a>
                    @endcan
                </div>
            </div>

            {{-- BLOK PENCARIAN BARU --}}
            <div class="card-body border-bottom bg-light py-2">
                <form action="{{ route('voters.index') }}" method="GET" class="form-inline" id="search-form">
                    <div class="input-group input-group-sm w-100" style="max-width: 400px;">
                        <input type="text" id="live-search" name="search" class="form-control" placeholder="Cari nama atau kode pemilih..." value="{{ $search ?? '' }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search" id="search-icon"></i>
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('voters.index') }}" class="btn btn-danger" title="Reset Pencarian">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            {{-- AKHIR BLOK PENCARIAN --}}
            
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap table-striped">
                    <thead>
                        <tr>
                            <th>Kode Pemilih</th>
                            <th>Nama Lengkap</th>
                            <th>Alamat</th>
                            <th>Wilayah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($voters as $voter)
                            <tr>
                                <td><code>{{ $voter->voter_code }}</code></td>
                                <td>{{ $voter->name }}</td>
                                <td>{{ $voter->address ?? '-' }}</td>
                                <td>{{ $voter->region->name ?? '-' }}</td>
                                <td>
                                    @if($voter->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manage_voter')
                                        <a href="{{ route('voters.edit', $voter) }}" class="btn btn-xs btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @if($voter->is_active)
                                            <form action="{{ route('voters.destroy', $voter) }}" method="POST" class="d-inline" onsubmit="return confirm('Nonaktifkan pemilih ini?')">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger ml-1">
                                                    <i class="fas fa-ban"></i> Nonaktifkan
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-muted text-xs"><i class="fas fa-lock"></i> Read Only</span>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada data pemilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $voters->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cari elemen berdasarkan ID
        const searchInput = document.getElementById('live-search');
        const searchIcon = document.getElementById('search-icon');
        const searchForm = document.getElementById('search-form');
        let debounceTimer;

        // 1. Pastikan form ada sebelum mencegah tombol Enter
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        }

        // 2. Pastikan kotak input ada sebelum memasang Live Search
        if (searchInput && searchIcon) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                
                // Ubah icon menjadi animasi loading putar
                searchIcon.className = 'fas fa-spinner fa-spin';

                // Beri jeda 500ms setelah selesai mengetik (Debounce)
                debounceTimer = setTimeout(() => {
                    const query = this.value;
                    const url = "{{ route('voters.index') }}?search=" + encodeURIComponent(query);

                    // Minta data ke server secara diam-diam (AJAX)
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.text())
                        .then(html => {
                            // Ekstrak HTML yang baru
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            // Cek apakah tabel ditemukan sebelum menimpa
                            const newTable = doc.querySelector('.table-responsive');
                            if (newTable) {
                                document.querySelector('.table-responsive').innerHTML = newTable.innerHTML;
                            }
                            
                            // Cek apakah pagination ditemukan sebelum menimpa
                            const currentFooter = document.querySelector('.card-footer');
                            const newFooter = doc.querySelector('.card-footer');
                            if (currentFooter && newFooter) {
                                currentFooter.innerHTML = newFooter.innerHTML;
                            }

                            // Kembalikan icon pencarian
                            searchIcon.className = 'fas fa-search';

                            // Update URL di browser tanpa reload
                            window.history.pushState({path: url}, '', url);
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                            searchIcon.className = 'fas fa-search';
                        });
                }, 500);
            });
        }
    });
</script>
@endpush

@endsection