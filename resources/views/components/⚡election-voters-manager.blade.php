<?php

use Livewire\Component;
use App\Models\Voter;
use App\Models\ElectionVoter;

new class extends Component
{
    public $election;
    public $searchAvailable = '';
    public $searchAssigned = '';
    public $selectedVoters = []; // Untuk checkbox pendaftaran

    public function mount($election)
    {
        $this->election = $election;
    }

    public function registerVoters()
    {
        foreach ($this->selectedVoters as $voterId) {
            ElectionVoter::create([
                'election_id' => $this->election->id,
                'voter_id' => $voterId,
            ]);
        }
        $this->selectedVoters = []; // Reset checkbox
        session()->flash('status', 'Pemilih berhasil didaftarkan.');
    }

    public function removeVoter($id)
    {
        $ev = ElectionVoter::findOrFail($id);
        if (!$ev->has_voted) {
            $ev->delete();
            session()->flash('status', 'Pemilih berhasil dikeluarkan.');
        }
    }

    public function render()
    {
        // Ambil ID voter yang sudah terdaftar
        $assignedIds = ElectionVoter::where('election_id', $this->election->id)
            ->pluck('voter_id');

        // Query Belum Terdaftar
        $availableVoters = Voter::whereNotIn('id', $assignedIds)
            ->where('name', 'like', '%' . $this->searchAvailable . '%')
            ->get();

        // Query Sudah Terdaftar
        $assignedVoters = ElectionVoter::where('election_id', $this->election->id)
            ->whereHas('voter', function ($q) {
                $q->where('name', 'like', '%' . $this->searchAssigned . '%');
            })
            ->get();

        return view('livewire.election-voters-manager', compact('availableVoters', 'assignedVoters'));
    }
};
?>

<div>
    {{-- Let all your things have their places; let each part of your business have its time. - Benjamin Franklin --}}
</div>