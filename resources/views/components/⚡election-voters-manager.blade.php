<?php

use Livewire\Component;
use App\Models\Voter;
use App\Models\ElectionVoter;
use App\Services\AuditLogger;

new class extends Component
{
    public $election;
    public $searchAvailable = '';
    public $searchAssigned = '';
    public $selectedVoters = [];
    public $channelChoices = []; // default pilihan channel saat daftarkan

    public function mount($election)
    {
        $this->election = $election;
    }

    public function registerVoters()
    {
        abort_if($this->election->status !== 'draft', 403, 'Pemilih hanya bisa didaftarkan selama pemilihan masih draft.');

        foreach ($this->selectedVoters as $voterId) {
            $channel = $this->channelChoices[$voterId] ?? 'tps';

            $ev = ElectionVoter::firstOrCreate(
                ['election_id' => $this->election->id, 'voter_id' => $voterId],
                ['allowed_channel' => $channel, 'has_voted' => false]
            );

            if (in_array($channel, ['remote', 'both']) && ! $ev->invitation_token) {
                $ev->invitation_token = \Illuminate\Support\Str::random(40);
                $ev->save();
            }
        }

        $this->selectedVoters = [];
        session()->flash('status', 'Pemilih berhasil didaftarkan.');
    }

    public function changeChannel($evId, $channel)
    {
        abort_if($this->election->status !== 'draft', 403, 'Jalur pemilih hanya bisa diubah selama pemilihan masih draft.');
        abort_if(! in_array($channel, ['tps', 'remote', 'both']), 422);

        $ev = ElectionVoter::findOrFail($evId);
        abort_if($ev->has_voted, 403, 'Pemilih yang sudah memilih tidak bisa diubah jalurnya.');

        $ev->allowed_channel = $channel;
        $ev->save();

        session()->flash('status', 'Jalur pemilih diperbarui.');
    }

    public function removeVoter($id)
    {
        abort_if($this->election->status !== 'draft', 403, 'Daftar pemilih hanya bisa diubah selama pemilihan masih draft.');

        $ev = ElectionVoter::findOrFail($id);
        if (! $ev->has_voted) {
            $ev->delete();
            AuditLogger::log('election_voter_remove', "Pemilih dikeluarkan dari pemilihan: {$this->election->title}", ['election_id' => $this->election->id]);
            session()->flash('status', 'Pemilih berhasil dikeluarkan.');
        }
    }

    public function render()
    {
        $assignedIds = ElectionVoter::where('election_id', $this->election->id)->pluck('voter_id');

        $availableVoters = Voter::where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->where('name', 'like', '%'.$this->searchAvailable.'%')
            ->get();

        $assignedVoters = ElectionVoter::where('election_id', $this->election->id)
            ->with('voter')
            ->whereHas('voter', function ($q) {
                $q->where('name', 'like', '%'.$this->searchAssigned.'%');
            })
            ->get();

        return view('livewire.election-voters-manager', compact('availableVoters', 'assignedVoters'));
    }
};
?>

<div>
    {{-- Let all your things have their places; let each part of your business have its time. - Benjamin Franklin --}}
</div>