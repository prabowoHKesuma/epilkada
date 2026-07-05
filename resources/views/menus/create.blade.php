<x-app-layout>
    <h1 class="mb-3">Tambah Menu</h1>

    <form action="{{ route('menus.store') }}" method="POST">
        @csrf

        <div class="form-group mb-2">
            <label>Judul Menu</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="form-group mb-2">
            <label>Menu Key (unik, tanpa spasi, contoh: manage_voters)</label>
            <input type="text" name="menu_key" class="form-control" value="{{ old('menu_key') }}" required>
        </div>

        <div class="form-group mb-2">
            <label>URL (contoh: /voters)</label>
            <input type="text" name="url" class="form-control" value="{{ old('url') }}">
        </div>

        <div class="form-group mb-2">
            <label>Icon Class (Font Awesome, contoh: fas fa-users)</label>
            <input type="text" name="icon_class" class="form-control" value="{{ old('icon_class', 'fas fa-circle') }}">
        </div>

        <div class="form-group mb-2">
            <label>Induk Menu (kosongkan jika ini menu utama)</label>
            <select name="parent_id" class="form-control">
                <option value="">-- Tidak ada (menu utama) --</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-2">
            <label>Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
        </div>

        <div class="form-group mb-3">
            <label>Role yang Bisa Lihat Menu Ini</label><br>
            @foreach ($roles as $role)
                <label class="mr-3">
                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"> {{ $role->name }}
                </label>
            @endforeach
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</x-app-layout>