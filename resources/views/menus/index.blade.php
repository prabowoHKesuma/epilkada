@extends('layouts.admin')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Kelola Menu</h1>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5><i class="icon fas fa-check"></i> Sukses!</h5>
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">Struktur Navigasi Sistem</h3>
        
        <div class="card-tools">
            <a href="{{ route('menus.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-1"></i> Tambah Menu
            </a>
        </div>
      </div>
      
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap table-bordered">
          <thead class="bg-light">
            <tr>
              <th>Judul Menu</th>
              <th>URL</th>
              <th class="text-center">Urutan</th>
              <th class="text-center">Status</th>
              <th class="text-center" style="width: 180px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($menus as $menu)
                <tr>
                    <td><strong><i class="{{ $menu->icon_class ?? 'fas fa-folder' }} mr-2 text-muted"></i> {{ $menu->title }}</strong></td>
                    <td><code>{{ $menu->url ?? '#' }}</code></td>
                    <td class="text-center">{{ $menu->sort_order }}</td>
                    <td class="text-center">
                        @if($menu->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('menus.edit', $menu) }}" class="btn btn-xs btn-info">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu induk ini beserta anak-anaknya?')">
                            @csrf 
                            @method('DELETE')
                            <button class="btn btn-xs btn-danger ml-1">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                
                @foreach ($menu->children as $child)
                    <tr class="bg-light">
                        <td class="pl-4">
                            <i class="fas fa-level-up-alt fa-rotate-90 mr-2 text-secondary"></i> 
                            {{ $child->title }}
                        </td>
                        <td><code>{{ $child->url }}</code></td>
                        <td class="text-center">{{ $child->sort_order }}</td>
                        <td class="text-center">
                            @if($child->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('menus.edit', $child) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('menus.destroy', $child) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus anak menu ini?')">
                                @csrf 
                                @method('DELETE')
                                <button class="btn btn-xs btn-danger ml-1">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada struktur menu yang dikonfigurasi.</td>
                </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    
  </div>
</div>
@endsection