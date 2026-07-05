<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Election;
use App\Models\ElectionVoter;
use App\Models\TpsBoothToken;
use Illuminate\Support\Str;
use App\Services\AuditLogger;

new class extends Component
{
    use WithPagination;

    public $election;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function mount(Election $election)
    {
        $this->election = $election;
    }

    public function revokeToken($tokenId)
    {
        $token = \App\Models\TpsBoothToken::find($tokenId);
        
        // Hanya bisa di-revoke jika belum digunakan (opsional, sesuaikan bisnis proses Anda)
        if ($token) {
            $token->delete();
            session()->flash('status', 'Token berhasil dibatalkan.');
        }
    }

    public function render()
    {
        // Eager loading relasi token agar tidak N+1 query
        $eligibleVoters = $this->election->eligibleVoters()
            ->with('latestToken') 
            ->whereHas('voter', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.tps-token-table', compact('eligibleVoters'));
    }

    public function terbitkanToken($electionVoterId)
    {
        // 1. Validasi Status Pemilihan
        if ($this->election->status !== 'open') {
            session()->flash('error', 'Token hanya bisa diterbitkan saat pemilihan berstatus open.');
            return;
        }

        // 2. Ambil data pemilih
        $electionVoter = ElectionVoter::findOrFail($electionVoterId);

        // 3. Validasi Keamanan
        if ($electionVoter->election_id !== $this->election->id) {
            abort(403);
        }
        
        if ($electionVoter->has_voted) {
            session()->flash('error', 'Pemilih ini sudah tercatat memilih.');
            return;
        }

        // 4. Generate Token
        $rawToken = strtoupper(Str::random(8));

        // 5. Simpan ke Database
        TpsBoothToken::create([
            'election_id' => $this->election->id,
            'election_voter_id' => $electionVoter->id,
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => now()->addMinutes(10),
            'created_by' => auth()->id(),
        ]);

        // 6. Audit Log
        AuditLogger::log(
            'issue_tps_token',
            "Token diterbitkan untuk pemilih {$electionVoter->voter->name} ({$electionVoter->voter->voter_code})",
            ['election_id' => $this->election->id]
        );

        // 7. Flash Session untuk Tampilan
        session()->flash('status', 'Token berhasil diterbitkan.');
        session()->flash('generated_token', $rawToken);
        session()->flash('voter_name', $electionVoter->voter->name);
    }
};
?>

<div>
    {{-- Very little is needed to make a happy life. - Marcus Aurelius --}}
</div>