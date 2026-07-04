<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-xl font-bold mb-4">Tambah Kandidat — {{ $election->title }}</h1>

        <form action="{{ route('candidates.store', $election) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <x-input-label for="number_order" value="Nomor Urut" />
                <x-text-input id="number_order" type="number" name="number_order" class="w-full" :value="old('number_order')" required />
                <x-input-error :messages="$errors->get('number_order')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="name" value="Nama Kandidat" />
                <x-text-input id="name" name="name" class="w-full" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="photo" value="Foto Kandidat" />
                <input type="file" id="photo" name="photo" class="w-full border rounded p-2" accept="image/*">
                <x-input-error :messages="$errors->get('photo')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="vision" value="Visi" />
                <textarea id="vision" name="vision" class="w-full border rounded" rows="3">{{ old('vision') }}</textarea>
            </div>

            <div class="mb-4">
                <x-input-label for="mission" value="Misi" />
                <textarea id="mission" name="mission" class="w-full border rounded" rows="3">{{ old('mission') }}</textarea>
            </div>

            <x-primary-button>Simpan</x-primary-button>
        </form>
    </div>
</x-app-layout>