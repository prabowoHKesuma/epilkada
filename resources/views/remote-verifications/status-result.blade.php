<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
        <h1 class="text-xl font-bold mb-4 text-center">Status Verifikasi</h1>

        @if($rv->status === 'pending')
            <p class="text-center text-yellow-700 bg-yellow-50 p-3 rounded">Masih menunggu verifikasi petugas. Silakan cek lagi beberapa saat lagi.</p>
        @elseif($rv->status === 'rejected')
            <p class="text-center text-red-700 bg-red-50 p-3 rounded">Pengajuan Anda ditolak.<br>Alasan: {{ $rv->reject_reason }}</p>
        @elseif($rv->status === 'approved')
            @if($token)
                <p class="text-center text-green-700 bg-green-50 p-3 rounded mb-3">Verifikasi disetujui! Ini token voting Anda:</p>
                <p class="text-3xl font-mono font-bold tracking-widest text-center mb-3">{{ $token }}</p>
                <p class="text-xs text-gray-500 text-center mb-3">Catat token ini sekarang — halaman ini tidak akan menampilkannya lagi setelah ditutup. Berlaku 2 hari.</p>
                <a href="{{ route('voting.token-form.remote') }}" class="block text-center py-3 bg-indigo-600 text-white rounded font-semibold">Lanjut ke Bilik Suara Remote</a>
            @else
                <p class="text-center text-gray-600">Verifikasi disetujui, tapi token sudah pernah diambil atau kedaluwarsa. Hubungi panitia jika ini keliru.</p>
            @endif
        @endif
    </div>
</x-guest-layout>