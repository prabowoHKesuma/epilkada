<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Daftar Pemilihan</h1>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ $errors->first() }}</div>
        @endif

        <a href="{{ route('elections.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">+ Buat Pemilihan</a>

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Judul</th>
                    <th class="p-2 text-left">Status</th>
                    <th class="p-2 text-left">Jumlah Kandidat</th>
                    <th class="p-2 text-left">Periode</th>
                    <th class="p-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($elections as $election)
                    <tr class="border-t">
                        <td class="p-2">{{ $election->title }}</td>
                        <td class="p-2 uppercase text-xs font-semibold">{{ $election->status }}</td>
                        <td class="p-2">{{ $election->candidates_count }}</td>
                        <td class="p-2">{{ $election->start_at->format('d M Y') }} - {{ $election->end_at->format('d M Y') }}</td>
                        <td class="p-2 space-x-2">
                            <a href="{{ route('elections.show', $election) }}" class="text-indigo-600">Detail</a>
                            @if($election->status === 'draft')
                                <a href="{{ route('elections.edit', $election) }}" class="text-blue-600">Edit</a>
                                <form action="{{ route('elections.destroy', $election) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pemilihan ini? Tindakan tidak bisa dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $elections->links() }}</div>
    </div>
</x-app-layout>