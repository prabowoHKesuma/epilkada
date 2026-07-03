<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Daftar Petugas</h1>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <a href="{{ route('users.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">+ Tambah Petugas</a>

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Nama</th>
                    <th class="p-2 text-left">Username</th>
                    <th class="p-2 text-left">Role</th>
                    <th class="p-2 text-left">Wilayah</th>
                    <th class="p-2 text-left">Status</th>
                    <th class="p-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-t">
                        <td class="p-2">{{ $user->name }}</td>
                        <td class="p-2">{{ $user->username }}</td>
                        <td class="p-2">{{ $user->roles->pluck('name')->join(', ') }}</td>
                        <td class="p-2">{{ $user->region->name ?? '-' }}</td>
                        <td class="p-2">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="p-2">
                            <a href="{{ route('users.edit', $user) }}" class="text-blue-600">Edit</a>

                            @if($user->is_active)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan petugas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 ml-2">Nonaktifkan</button>
                                </form>
                            @else
                                <form action="{{ route('users.activate', $user) }}" method="POST" class="inline" onsubmit="return confirm('Aktifkan kembali petugas ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-green-600 ml-2">Aktifkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>