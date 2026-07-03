<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-xl font-bold mb-4">Tambah Petugas</h1>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input id="name" name="name" class="w-full" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="username" value="Username" />
                <x-text-input id="username" name="username" class="w-full" :value="old('username')" required />
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" type="password" name="password" class="w-full" required />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="organization_id" value="Organisasi" />
                <select name="organization_id" id="organization_id" class="w-full border rounded">
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}" @selected(old('organization_id') == $org->id)>{{ $org->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('organization_id')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="region_id" value="Wilayah Penugasan" />
                <select name="region_id" id="region_id" class="w-full border rounded">
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>
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
                        <option value="{{ $r->name }}" @selected(old('role') == $r->name)>{{ $r->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-1" />
            </div>

            <x-primary-button>Simpan</x-primary-button>
        </form>
    </div>
</x-app-layout>