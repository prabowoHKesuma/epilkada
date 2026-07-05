<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Log Aktivitas Sistem</h1>

        <table class="w-full border text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Waktu</th>
                    <th class="p-2 text-left">User</th>
                    <th class="p-2 text-left">Aksi</th>
                    <th class="p-2 text-left">Deskripsi</th>
                    <th class="p-2 text-left">IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr class="border-t">
                        <td class="p-2">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="p-2">{{ $log->user->username ?? 'Anonim/Sistem' }}</td>
                        <td class="p-2"><code>{{ $log->action }}</code></td>
                        <td class="p-2">{{ $log->description }}</td>
                        <td class="p-2">{{ $log->ip_address }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</x-app-layout>