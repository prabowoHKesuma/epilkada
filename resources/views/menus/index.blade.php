<x-app-layout>
    <h1 class="mb-3">Kelola Menu</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <a href="{{ route('menus.create') }}" class="btn btn-primary mb-3">+ Tambah Menu</a>

    <table class="table table-bordered bg-white">
        <thead>
            <tr><th>Judul</th><th>URL</th><th>Urutan</th><th>Aktif</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            @foreach ($menus as $menu)
                <tr>
                    <td>{{ $menu->title }}</td>
                    <td>{{ $menu->url }}</td>
                    <td>{{ $menu->sort_order }}</td>
                    <td>{{ $menu->is_active ? 'Ya' : 'Tidak' }}</td>
                    <td>
                        <a href="{{ route('menus.edit', $menu) }}" class="btn btn-sm btn-info">Edit</a>
                        <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @foreach ($menu->children as $child)
                    <tr class="bg-light">
                        <td>— {{ $child->title }}</td>
                        <td>{{ $child->url }}</td>
                        <td>{{ $child->sort_order }}</td>
                        <td>{{ $child->is_active ? 'Ya' : 'Tidak' }}</td>
                        <td>
                            <a href="{{ route('menus.edit', $child) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('menus.destroy', $child) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</x-app-layout>