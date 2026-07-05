<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Daftar Pemilih — {{ $election->title }}</h1>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <div class="grid grid-cols-2 gap-6">
            <div>
                <h2 class="font-semibold mb-2">Belum Terdaftar ({{ $availableVoters->count() }})</h2>
                <form action="{{ route('election-voters.store', $election) }}" method="POST">
                    @csrf
                    <div class="max-h-96 overflow-y-auto border rounded p-2 mb-2">
                        @foreach ($availableVoters as $voter)
                            <label class="block">
                                <input type="checkbox" name="voter_ids[]" value="{{ $voter->id }}">
                                {{ $voter->name }} ({{ $voter->voter_code }})
                            </label>
                        @endforeach
                    </div>
                    <x-primary-button>Daftarkan yang Dicentang</x-primary-button>
                </form>
            </div>

            <div>
                <h2 class="font-semibold mb-2">Sudah Terdaftar ({{ $assignedVoters->count() }})</h2>
                <div class="max-h-96 overflow-y-auto border rounded p-2">
                    @foreach ($assignedVoters as $ev)
                        <div class="flex justify-between items-center py-1">
                            <span>{{ $ev->voter->name }} — {{ $ev->has_voted ? 'Sudah memilih' : 'Belum memilih' }}</span>
                            @if(!$ev->has_voted && $election->status === 'draft')
                                <form action="{{ route('election-voters.destroy', [$election, $ev]) }}" method="POST" onsubmit="return confirm('Keluarkan pemilih ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 text-sm">Keluarkan</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>