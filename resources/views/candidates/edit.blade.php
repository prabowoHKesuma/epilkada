<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-xl font-bold mb-4">Edit Kandidat — {{ $election->title }}</h1>

        <form action="{{ route('candidates.update', [$election, $candidate]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <x-input-label for="number_order" value="Nomor Urut" />
                <x-text-input id="number_order" type="number" name="number_order" class="w-full" :value="old('number_order', $candidate->number_order)" required />
                <x-input-error :messages="$errors->get('number_order')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="name" value="Nama Kandidat" />
                <x-text-input id="name" name="name" class="w-full" :value="old('name', $candidate->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="photo" value="Foto Kandidat (kosongkan jika tidak ingin ganti)" />
                @if($candidate->photo)
                    <img src="{{ Storage::url($candidate->photo) }}" class="w-32 h-32 object-cover rounded mb-2">
                @endif
                <input type="file" id="photo" name="photo" class="w-full border rounded p-2" accept="image/*">
                <x-input-error :messages="$errors->get('photo')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="vision" value="Visi" />
                <textarea id="vision" name="vision" class="w-full border rounded" rows="3">{{ old('vision', $candidate->vision) }}</textarea>
            </div>

            <div class="mb-4">
                <x-input-label for="mission" value="Misi" />
                <textarea id="mission" name="mission" class="w-full border rounded" rows="3">{{ old('mission', $candidate->mission) }}</textarea>
            </div>

            <x-primary-button>Simpan Perubahan</x-primary-button>
            <a href="{{ route('elections.show', $election) }}" class="ml-3 text-gray-600">Batal</a>
        </form>
    </div>
</x-app-layout>