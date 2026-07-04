<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-xl font-bold mb-4">Edit Pemilihan</h1>

        <form action="{{ route('elections.update', $election) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <x-input-label for="title" value="Judul Pemilihan" />
                <x-text-input id="title" name="title" class="w-full" :value="old('title', $election->title)" required />
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="description" value="Deskripsi" />
                <textarea id="description" name="description" class="w-full border rounded" rows="3">{{ old('description', $election->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="start_at" value="Tanggal Mulai" />
                <x-text-input id="start_at" type="datetime-local" name="start_at" class="w-full" :value="old('start_at', $election->start_at->format('Y-m-d\TH:i'))" required />
                <x-input-error :messages="$errors->get('start_at')" class="mt-1" />
            </div>

            <div class="mb-4">
                <x-input-label for="end_at" value="Tanggal Selesai" />
                <x-text-input id="end_at" type="datetime-local" name="end_at" class="w-full" :value="old('end_at', $election->end_at->format('Y-m-d\TH:i'))" required />
                <x-input-error :messages="$errors->get('end_at')" class="mt-1" />
            </div>

            <x-primary-button>Simpan Perubahan</x-primary-button>
            <a href="{{ route('elections.index') }}" class="ml-3 text-gray-600">Batal</a>
        </form>
    </div>
</x-app-layout>