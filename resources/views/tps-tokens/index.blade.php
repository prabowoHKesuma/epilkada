<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Terbitkan Token TPS — {{ $election->title }}</h1>

        @if (session('generated_token'))
            <div class="mb-6 p-6 bg-indigo-50 border-2 border-indigo-400 rounded text-center">
                <p class="text-sm text-gray-600">Token untuk: <strong>{{ session('voter_name') }}</strong></p>
                <p class="text-4xl font-mono font-bold tracking-widest my-3">{{ session('generated_token') }}</p>
                <p class="text-xs text-gray-500">Berlaku 10 menit. Berikan kode ini ke pemilih untuk diketik di bilik suara.</p>
            </div>
        @endif

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Nama Pemilih</th>
                    <th class="p-2 text-left">Kode</th>
                    <th class="p-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($eligibleVoters as $ev)
                    <tr class="border-t">
                        <td class="p-2">{{ $ev->voter->name }}</td>
                        <td class="p-2">{{ $ev->voter->voter_code }}</td>
                        <td class="p-2">
                            <form action="{{ route('tps-tokens.store', $election) }}" method="POST" onsubmit="return confirm('Terbitkan token untuk {{ $ev->voter->name }}?')">
                                @csrf
                                <input type="hidden" name="election_voter_id" value="{{ $ev->id }}">
                                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Terbitkan Token</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($eligibleVoters->isEmpty())
            <p class="text-gray-500">Tidak ada pemilih yang tersisa untuk diterbitkan token (semua sudah memilih atau sudah punya token aktif).</p>
        @endif
    </div>
</x-app-layout>