<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
        <h1 class="text-xl font-bold mb-2 text-center">Bilik Suara Remote</h1>
        <p class="text-sm text-gray-600 text-center mb-6">Masukkan token yang Anda dapat setelah verifikasi disetujui</p>

        <x-input-error :messages="$errors->get('token')" class="mb-3" />

        <form action="{{ route('voting.verify.remote') }}" method="POST">
            @csrf
            <input type="text" name="token" maxlength="10" class="w-full text-center text-2xl font-mono tracking-widest border rounded p-3 mb-4" placeholder="XXXXXXXXXX" autofocus required>
            <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded font-semibold">Masuk ke Bilik Suara</button>
        </form>
    </div>
</x-guest-layout>