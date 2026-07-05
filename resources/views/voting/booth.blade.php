<x-guest-layout>
    <div class="max-w-3xl mx-auto mt-6 p-6">
        <h1 class="text-xl font-bold mb-2 text-center">{{ $election->title }}</h1>
        <p class="text-sm text-gray-600 text-center mb-6">Klik kandidat pilihan Anda, lalu tekan "Simpan Pilihan"</p>

        <form action="{{ route('voting.submit') }}" method="POST" id="voteForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                @foreach($election->candidates as $candidate)
                    <div class="candidate-card border-2 rounded-lg p-4 cursor-pointer" data-id="{{ $candidate->id }}" data-name="No. {{ $candidate->number_order }} — {{ $candidate->name }}">
                        <input type="radio" name="candidate_id" value="{{ $candidate->id }}" class="hidden">
                        <span class="font-semibold">No. {{ $candidate->number_order }} — {{ $candidate->name }}</span>
                        @if($candidate->photo)
                            <img src="{{ Storage::url($candidate->photo) }}" class="w-full h-40 object-cover rounded mt-2">
                        @endif
                    </div>
                @endforeach
            </div>

            <div id="statusBox" class="mb-4 p-3 rounded bg-gray-100 text-gray-600 text-center">
                Belum ada kandidat dipilih.
            </div>

            <button type="button" id="btnSimpan" class="w-full py-3 bg-indigo-600 text-white rounded font-semibold mb-3" disabled>
                Simpan Pilihan
            </button>

            <button type="submit" id="btnKirim" class="w-full py-3 bg-green-600 text-white rounded font-semibold text-lg hidden">
                Konfirmasi & Kirim Suara
            </button>
        </form>
    </div>

    <script>
        const cards = document.querySelectorAll('.candidate-card');
        const statusBox = document.getElementById('statusBox');
        const btnSimpan = document.getElementById('btnSimpan');
        const btnKirim = document.getElementById('btnKirim');
        let selectedCard = null;

        cards.forEach(card => {
            card.addEventListener('click', () => {
                cards.forEach(c => c.classList.remove('border-indigo-600', 'bg-indigo-50'));
                card.classList.add('border-indigo-600', 'bg-indigo-50');
                card.querySelector('input[type=radio]').checked = true;
                selectedCard = card;

                statusBox.textContent = 'Dipilih: ' + card.dataset.name + ' (klik Simpan Pilihan untuk lanjut)';
                statusBox.classList.remove('bg-gray-100', 'text-gray-600');
                statusBox.classList.add('bg-blue-100', 'text-blue-800');

                btnSimpan.disabled = false;
                btnKirim.classList.add('hidden'); // reset kalau ganti pilihan setelah sempat "disimpan"
            });
        });

        btnSimpan.addEventListener('click', () => {
            if (!selectedCard) return;

            statusBox.textContent = 'Pilihan tersimpan: ' + selectedCard.dataset.name + '. Periksa lagi, lalu tekan tombol hijau untuk mengirim.';
            statusBox.classList.remove('bg-blue-100', 'text-blue-800');
            statusBox.classList.add('bg-green-100', 'text-green-800');

            btnSimpan.classList.add('hidden');
            btnKirim.classList.remove('hidden');
        });

        document.getElementById('voteForm').addEventListener('submit', function (e) {
            if (!confirm('Yakin kirim suara untuk: ' + selectedCard.dataset.name + '?\n\nSuara TIDAK BISA diubah setelah ini.')) {
                e.preventDefault();
            }
        });
    </script>
</x-guest-layout>