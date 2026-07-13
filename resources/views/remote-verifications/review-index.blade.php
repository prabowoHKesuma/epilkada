<x-app-layout>
    <h1 class="mb-3">Verifikasi Pemilih Remote</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @foreach ($pendings as $rv)
        <div class="card mb-3 p-3">
            <p><strong>{{ $rv->voter->name ?? 'Data pemilih tidak ditemukan' }}</strong> ({{ $rv->voter->voter_code ?? '-' }}) — Kode: {{ $rv->verification_code }}</p>
            <p>
                <a href="{{ route('remote-review.document', [$rv, 'ktp']) }}" target="_blank" class="btn btn-sm btn-secondary">Lihat KTP</a>
                <a href="{{ route('remote-review.document', [$rv, 'selfie']) }}" target="_blank" class="btn btn-sm btn-secondary">Lihat Selfie</a>
            </p>
            <p class="text-sm">
                Tahap 1: {{ $rv->verified_by_1 ? 'Sudah (oleh user #'.$rv->verified_by_1.')' : 'Belum' }}
            </p>

            @if(!$rv->verified_by_1)
                <form action="{{ route('remote-review.approve1', $rv) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-primary">Setujui Tahap 1</button>
                </form>
            @elseif($rv->verified_by_1 !== auth()->id())
                <form action="{{ route('remote-review.approve2', $rv) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-success">Setujui Tahap 2 (Final)</button>
                </form>
            @else
                <p class="text-muted text-sm">Anda sudah approve tahap 1 — tahap 2 perlu petugas lain.</p>
            @endif

            <form action="{{ route('remote-review.reject', $rv) }}" method="POST" class="d-inline mt-2">
                @csrf
                <input type="text" name="reject_reason" placeholder="Alasan tolak" class="form-control d-inline w-auto" style="width:250px" required>
                <button class="btn btn-sm btn-danger">Tolak</button>
            </form>
        </div>
    @endforeach
    @if($pendings->isEmpty())
        <p class="text-muted">Belum ada pengajuan verifikasi remote yang perlu diproses.</p>
    @endif
</x-app-layout>