<div>
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="icon fas fa-check"></i> {{ session('status') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Belum Terdaftar ({{ $availableVoters->count() }})</h3>
                </div>
                <div class="card-body p-0">
                    <div class="p-2 border-bottom">
                        <input type="text" wire:model.live.debounce.300ms="searchAvailable" class="form-control form-control-sm" placeholder="Cari nama pemilih...">
                    </div>
                    
                    <div style="max-height: 350px; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            @foreach ($availableVoters as $voter)
                                <div class="list-group-item py-2 px-3">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" wire:model="selectedVoters" id="voter_{{ $voter->id }}" value="{{ $voter->id }}">
                                        <label class="custom-control-label font-weight-normal" for="voter_{{ $voter->id }}">
                                            <strong>{{ $voter->name }}</strong> 
                                            <span class="text-muted d-block text-sm">Kode: <code>{{ $voter->voter_code }}</code></span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button wire:click="registerVoters" class="btn btn-primary btn-block" >
                        <i class="fas fa-user-check"></i> Masukkan Pemilih yang Dicentang
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">Sudah Terdaftar ({{ $assignedVoters->count() }})</h3>
                </div>
                <div class="card-body p-0">
                    <div class="p-2 border-bottom">
                        <input type="text" wire:model.live.debounce.300ms="searchAssigned" class="form-control form-control-sm" placeholder="Cari nama pemilih terdaftar...">
                    </div>

                    <div style="max-height: 350px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @foreach ($assignedVoters as $ev)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                    <div>
                                        <span class="font-weight-bold d-block">{{ $ev->voter->name }}</span>
                                        <span class="badge {{ $ev->has_voted ? 'badge-success' : 'badge-warning' }}">
                                            {{ $ev->has_voted ? 'Sudah Memilih' : 'Belum Memilih' }}
                                        </span>
                                    </div>
                                    @if(!$ev->has_voted && $election->status === 'draft')
                                        <button wire:click="removeVoter({{ $ev->id }})" wire:confirm="Keluarkan pemilih ini?" class="btn btn-xs btn-outline-danger">
                                            <i class="fas fa-user-minus"></i> Keluarkan
                                        </button>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>