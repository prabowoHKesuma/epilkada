<x-app-layout>
    <div class="p-6 max-w-2xl">
        <h1 class="text-xl font-bold mb-1">Hasil — {{ $election->title }}</h1>
        <p class="text-sm text-gray-600 mb-6">
            Partisipasi: {{ $totalSudahMemilih }} / {{ $totalTerdaftar }} pemilih terdaftar
            ({{ $totalTerdaftar > 0 ? round($totalSudahMemilih / $totalTerdaftar * 100, 1) : 0 }}%)
        </p>

        @foreach ($results as $candidate)
            @php
                $persen = $totalSuara > 0 ? round($candidate->ballots_count / $totalSuara * 100, 1) : 0;
            @endphp
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold">No. {{ $candidate->number_order }} — {{ $candidate->name }}</span>
                    <span>{{ $candidate->ballots_count }} suara ({{ $persen }}%)</span>
                </div>
                <div class="w-full bg-gray-200 rounded h-4">
                    <div class="bg-indigo-600 h-4 rounded" style="width: {{ $persen }}%"></div>
                </div>
            </div>
        @endforeach

        <p class="mt-6 text-sm text-gray-500">Total suara sah masuk: {{ $totalSuara }}</p>
    </div>
</x-app-layout>