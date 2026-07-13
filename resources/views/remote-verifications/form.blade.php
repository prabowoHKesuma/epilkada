<x-guest-layout>
    <div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded shadow">
        <h1 class="text-xl font-bold mb-4">Ajukan Verifikasi Pemilih Remote</h1>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('remote.submit', $election) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Kode Pemilih</label>
                <input type="text" name="voter_code" class="w-full border rounded p-2" required>
                <p class="text-xs text-gray-500">Lihat di surat undangan/pemberitahuan yang Anda terima.</p>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">NIK (16 digit)</label>
                <input type="text" name="nik" maxlength="16" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Foto KTP</label>
                <input type="file" name="ktp_photo" accept="image/*" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Foto Selfie (memegang KTP)</label>
                <input type="file" name="selfie_photo" accept="image/*" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label class="text-sm">
                    <input type="checkbox" name="consent_accepted" value="1" required>
                    Saya menyetujui data ini digunakan untuk keperluan verifikasi pemilih e-Pilkada.
                </label>
            </div>

            <button class="w-full py-3 bg-indigo-600 text-white rounded font-semibold">Ajukan Verifikasi</button>
        </form>
    </div>
</x-guest-layout>