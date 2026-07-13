<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow text-center">
        <h1 class="text-xl font-bold text-green-600 mb-3">Pengajuan Terkirim</h1>
        <p class="text-gray-600 mb-4">Simpan kode ini untuk mengecek status verifikasi Anda nanti:</p>
        <p class="text-3xl font-mono font-bold tracking-widest mb-4">{{ $verificationCode }}</p>
        <a href="{{ route('remote.status.form') }}" class="text-indigo-600 underline">Cek Status Sekarang</a>
    </div>
</x-guest-layout>