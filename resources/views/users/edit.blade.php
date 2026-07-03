<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-xl font-bold mb-4">Edit Petugas</h1>

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input id="name" name="name" class="w-full" :value="old('name', $user->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="username" value="Username (tidak bisa diubah)" />
                <x-text-input id="username" name="username" class="w-full bg-gray-100" value="{{ $user->username }}" readonly />
            </div>

            <div class="mb-3">
                <x-input-label for="password" value="Password Baru (kosongkan jika tidak ingin ubah)" />
                <x-text-input id="password" type="password" name="password" class="w-full" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="organization_id" value="Organisasi" />
                <select name="organization_id" id="organization_id" class="w-full border rounded">
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}" @selected(old('organization_id', $user->organization_id) == $org->id)>{{ $org->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('organization_id')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="region_id" value="Wilayah Penugasan" />
                <select name="region_id" id="region_id" class="w-full border rounded">
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected(old('region_id', $user->region_id) == $region->id)>
                            {{ str_repeat('— ', $region->level == 'rt' ? 2 : ($region->level == 'rw' ? 1 : 0)) }}{{ $region->name }} ({{ $region->level }})
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('region_id')" class="mt-1" />
            </div>

            <div class="mb-4">
                <x-input-label for="role" value="Role" />
                <select name="role" id="role" class="w-full border rounded">
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" @selected(old('role', $user->roles->first()->name ?? '') == $r->name)>{{ $r->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-1" />
            </div>

            <x-primary-button>Simpan Perubahan</x-primary-button>
            <a href="{{ route('users.index') }}" class="ml-3 text-gray-600">Batal</a>
        </form>
    </div>
</x-app-layout>