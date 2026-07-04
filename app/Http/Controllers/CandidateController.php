<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;
use App\Models\Candidate;
use App\Http\Requests\StoreCandidateRequest;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Election $election)
    {
        abort_if($election->status !== 'draft', 403, 'Kandidat hanya bisa ditambahkan selama pemilihan masih draft.');
        return view('candidates.create', compact('election'));
    }

    public function store(StoreCandidateRequest $request, Election $election)
    {
        abort_if($election->status !== 'draft', 403, 'Kandidat hanya bisa ditambahkan selama pemilihan masih draft.');

        $validated = $request->validated();

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('candidates', 'public');
        }

        Candidate::create([
            'election_id' => $election->id,
            'number_order' => $validated['number_order'],
            'name' => $validated['name'],
            'vision' => $validated['vision'] ?? null,
            'mission' => $validated['mission'] ?? null,
            'photo' => $photoPath,
            'is_active' => true,
        ]);

        return redirect()->route('elections.show', $election)->with('status', 'Kandidat berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Election $election, Candidate $candidate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Election $election, Candidate $candidate)
    {
        abort_if($election->status !== 'draft', 403, 'Kandidat hanya bisa diedit selama pemilihan masih draft.');
        return view('candidates.edit', compact('election', 'candidate'));
    }

    public function update(StoreCandidateRequest $request, Election $election, Candidate $candidate)
    {
        abort_if($election->status !== 'draft', 403, 'Kandidat hanya bisa diedit selama pemilihan masih draft.');

        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }
            $validated['photo'] = $request->file('photo')->store('candidates', 'public');
        }

        $candidate->update($validated);

        return redirect()->route('elections.show', $election)->with('status', 'Data kandidat diperbarui.');
    }

    public function destroy(Election $election, Candidate $candidate)
    {
        abort_if($election->status !== 'draft', 403, 'Kandidat hanya bisa dihapus selama pemilihan masih draft.');

        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo);
        }
        $candidate->delete();

        return redirect()->route('elections.show', $election)->with('status', 'Kandidat dihapus.');
    }
}
