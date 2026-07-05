<x-app-layout>
    <h1 class="mb-3">Edit Menu</h1>

    <form action="{{ route('menus.update', $menu) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-2">
            <label>Judul Menu</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $menu->title) }}" required>
        </div>

        <div class="form-group mb-2">
            <label>Menu Key (unik, tanpa spasi, contoh: manage_voters)</label>
            <input type="text" name="menu_key" class="form-control" value="{{ old('menu_key', $menu->menu_key) }}" required>
        </div>

        <div class="form-group mb-2">
            <label>URL (contoh: /voters)</label>
            <input type="text" name="url" class="form-control" value="{{ old('url', $menu->url) }}">
        </div>

        <div class="form-group mb-2">
            <label>Icon Class (Font Awesome, contoh: fas fa-users)</label>
            <input type="text" name="icon_class" class="form-control" value="{{ old('icon_class', $menu->icon_class) }}">
        </div>

        <div class="form-group mb-2">
            <label>Induk Menu (kosongkan jika ini menu utama)</label>
            <select name="parent_id" class="form-control">
                <option value="">-- Tidak ada (menu utama) --</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>
                        {{ $parent->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-2">
            <label>Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $menu->sort_order) }}">
        </div>

        <div class="form-group mb-3">
            <label>Role yang Bisa Lihat Menu Ini</label><br>
            @foreach ($roles as $role)
                <label class="mr-3">
                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" @checked(in_array($role->id, old('role_ids', $selectedRoleIds)))> {{ $role->name }}
                </label>
            @endforeach
        </div>

        <div class="form-group mb-2">
            <label>
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked($menu->is_active)>
                Aktif
            </label>
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</x-app-layout>