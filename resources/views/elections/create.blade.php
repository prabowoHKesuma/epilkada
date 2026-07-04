<x-app-layout>
    <div class="p-6 max-w-xl">
        <h1 class="text-xl font-bold mb-4">Buat Pemilihan Baru</h1>

        <form action="{{ route('elections.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <x-input-label for="title" value="Judul Pemilihan" />
                <x-text-input id="title" name="title" class="w-full" :value="old('title')" required />
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="description" value="Deskripsi" />
                <textarea id="description" name="description" class="w-full border rounded" rows="3">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div class="mb-3">
                <x-input-label for="start_at" value="Tanggal Mulai" />
                <x-text-input id="start_at" type="datetime-local" name="start_at" class="w-full" :value="old('start_at')" required />
                <x-input-error :messages="$errors->get('start_at')" class="mt-1" />
            </div>

            <div class="mb-4">
                <x-input-label for="end_at" value="Tanggal Selesai" />
                <x-text-input id="end_at" type="datetime-local" name="end_at" class="w-full" :value="old('end_at')" required />
                <x-input-error :messages="$errors->get('end_at')" class="mt-1" />
            </div>

            <x-primary-button>Simpan</x-primary-button>
        </form>
    </div>
</x-app-layout>