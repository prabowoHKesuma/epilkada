<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-2">{{ $election->title }}</h1>
        <p class="text-sm text-gray-600 mb-4">Status: <span class="font-semibold uppercase">{{ $election->status }}</span></p>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="mb-6 space-x-2">
            @if($election->status === 'draft')
                <form action="{{ route('elections.publish', $election) }}" method="POST" class="inline" onsubmit="return confirm('Buka pemilihan ini? Data tidak bisa diedit lagi setelah dibuka.')">
                    @csrf @method('PATCH')
                    <button class="px-3 py-2 bg-green-600 text-white rounded">Buka Pemilihan</button>
                </form>
            @elseif($election->status === 'open')
                <form action="{{ route('elections.close', $election) }}" method="POST" class="inline" onsubmit="return confirm('Tutup pemungutan suara?')">
                    @csrf @method('PATCH')
                    <button class="px-3 py-2 bg-yellow-600 text-white rounded">Tutup Pemungutan Suara</button>
                </form>
            @elseif($election->status === 'closed')
                <form action="{{ route('elections.finish', $election) }}" method="POST" class="inline" onsubmit="return confirm('Selesaikan pemilihan? Hasil akan final.')">
                    @csrf @method('PATCH')
                    <button class="px-3 py-2 bg-gray-800 text-white rounded">Selesaikan & Finalisasi</button>
                </form>
            @endif
        </div>

        <h2 class="text-lg font-semibold mb-2">Kandidat</h2>

        @if($election->status === 'draft')
            <a href="{{ route('candidates.create', $election) }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">+ Tambah Kandidat</a>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($election->candidates as $candidate)
                <div class="border rounded p-4">
                    @if($candidate->photo)
                        <img src="{{ Storage::url($candidate->photo) }}" class="w-full h-40 object-cover rounded mb-2">
                    @endif
                    <p class="font-semibold">No. {{ $candidate->number_order }} — {{ $candidate->name }}</p>
                    @if($election->status === 'draft')
                        <div class="mt-2 space-x-2">
                            <a href="{{ route('candidates.edit', [$election, $candidate]) }}" class="text-blue-600 text-sm">Edit</a>
                            <form action="{{ route('candidates.destroy', [$election, $candidate]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kandidat ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm">Hapus</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mb-4 space-x-2">
            <a href="{{ route('candidates.create', $election) }}" class="px-3 py-2 bg-gray-700 text-white rounded text-sm inline-block">Kelola Kandidat</a>
            <a href="{{ route('election-voters.index', $election) }}" class="px-3 py-2 bg-gray-700 text-white rounded text-sm inline-block">Kelola Pemilih</a>
            <a href="{{ route('tps-tokens.index', $election) }}" class="px-3 py-2 bg-gray-700 text-white rounded text-sm inline-block">Token TPS</a>
            @if(in_array($election->status, ['closed', 'finished']))
                <a href="{{ route('results.show', $election) }}" class="px-3 py-2 bg-green-700 text-white rounded text-sm inline-block">Lihat Hasil</a>
            @endif
        </div>
    </div>
</x-app-layout>