<x-app-layout>
    <h1 class="mb-3">Pilih Pemilihan — Terbitkan Token</h1>

    <table class="table table-bordered bg-white">
        <thead><tr><th>Judul Pemilihan</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach ($elections as $election)
                <tr>
                    <td>{{ $election->title }}</td>
                    <td><a href="{{ route('tps-tokens.index', $election) }}" class="btn btn-sm btn-primary">Terbitkan Token</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($elections->isEmpty())
        <p class="text-muted">Tidak ada pemilihan berstatus "open" saat ini.</p>
    @endif
</x-app-layout>