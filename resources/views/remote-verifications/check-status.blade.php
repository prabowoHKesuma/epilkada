<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
        <h1 class="text-xl font-bold mb-4 text-center">Cek Status Verifikasi</h1>

        <x-input-error :messages="$errors->get('verification_code')" class="mb-3" />

        <form action="{{ route('remote.status.check') }}" method="POST">
            @csrf
            <input type="text" name="verification_code" maxlength="8" class="w-full text-center text-xl font-mono tracking-widest border rounded p-3 mb-4" placeholder="KODE" required>
            <button class="w-full py-3 bg-indigo-600 text-white rounded font-semibold">Cek Status</button>
        </form>
    </div>
</x-guest-layout>