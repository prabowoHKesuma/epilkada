<div>
    <!-- Form Pencarian -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       class="form-control" 
                       placeholder="Cari nama pemilih...">
            </div>
        </div>
    </div>

    @if (session()->has('generated_token'))
        <div class="alert alert-success">
            Token untuk <strong>{{ session('voter_name') }}</strong>: 
            <h3 class="text-white">{{ session('generated_token') }}</h3>
        </div>
    @endif

<!-- Tabel Anda di bawah ini -->

    <!-- Tabel Data -->
    <div class="card card-outline card-primary">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Nama Pemilih</th>
                        <th>Kode Registrasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eligibleVoters as $ev)
                        @php
                            $token = $ev->latestToken;
                            $isExpired = $token && $token->expires_at < now();
                            $isValid = $token && !$isExpired && !$ev->has_voted;
                        @endphp
                        <tr>
                            <td class="align-middle"><strong>{{ $ev->voter->name }}</strong></td>
                            <td class="align-middle">
                                @if ($ev->has_voted)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> Sudah Memilih</span>
                                @elseif ($isValid)
                                    <span class="badge badge-warning text-dark">Aktif ({{ $token->expires_at->format('H:i') }})</span>
                                    <div class="font-mono font-bold">{{ $token->token_hash }}</div> <!-- Catatan: Jika ingin tampilkan hash, pastikan simpan plain-text atau sesuaikan -->
                                @else
                                    <span class="text-muted text-sm">Belum ada token</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($ev->has_voted)
                                    <button class="btn btn-sm btn-secondary" disabled>Selesai</button>
                                @elseif ($isValid)
                                    <button wire:click="revokeToken({{ $token->id }})" 
                                            wire:confirm="Yakin ingin membatalkan token ini?"
                                            class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Revoke
                                    </button>
                                @else
                                    <form wire:submit.prevent="terbitkanToken({{ $ev->id }})" 
                                        onsubmit="return confirm('Terbitkan token untuk {{ $ev->voter->name }}?')">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-key"></i> Terbitkan
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $eligibleVoters->links() }}
        </div>
    </div>
</div>