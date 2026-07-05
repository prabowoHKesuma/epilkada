<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Region;
use App\Http\Requests\StoreVoterRequest;
use App\Services\PiiHasher;
use Illuminate\Validation\ValidationException;
use App\Services\AuditLogger;

class VoterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voters = Voter::with('region')->paginate(20);
        return view('voters.index', compact('voters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::where('level', 'rt')->orWhere('level', 'rw')->get();
        return view('voters.create', compact('regions'));
    }

    public function store(StoreVoterRequest $request)
    {
        $validated = $request->validated();

        $nikHash = PiiHasher::hash($validated['nik']);

        if (Voter::where('nik_hash', $nikHash)->exists()) {
            throw ValidationException::withMessages([
                'nik' => 'NIK ini sudah terdaftar sebagai pemilih sebelumnya.',
            ]);
        }

        $voter = Voter::create([
            'voter_code' => 'PMH-'.strtoupper(uniqid()),
            'organization_id' => auth()->user()->organization_id,
            'region_id' => $validated['region_id'],
            'name' => $validated['name'],
            'nik_hash' => $nikHash,
            'kk_hash' => PiiHasher::hash($validated['kk']),
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'rt' => $validated['rt'] ?? null,
            'rw' => $validated['rw'] ?? null,
            'is_active' => true,
        ]);
        AuditLogger::log('voter_create', "Pemilih didaftarkan: {$voter->name}", ['voter_id' => $voter->id]);
        return redirect()->route('voters.index')->with('status', "Pemilih {$voter->name} berhasil didaftarkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voter $voter)
    {
        $regions = Region::where('level', 'rt')->orWhere('level', 'rw')->get();
        return view('voters.edit', compact('voter', 'regions'));
    }

    public function update(StoreVoterRequest $request, Voter $voter)
    {
        $validated = $request->validated();

        $nikHash = PiiHasher::hash($validated['nik']);

        if ($nikHash !== $voter->nik_hash && Voter::where('nik_hash', $nikHash)->exists()) {
            throw ValidationException::withMessages([
                'nik' => 'NIK ini sudah dipakai pemilih lain.',
            ]);
        }

        $voter->update([
            'name' => $validated['name'],
            'nik_hash' => $nikHash,
            'kk_hash' => PiiHasher::hash($validated['kk']),
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'region_id' => $validated['region_id'],
            'rt' => $validated['rt'] ?? null,
            'rw' => $validated['rw'] ?? null,
        ]);

        AuditLogger::log('voter_update', "Data pemilih diperbarui: {$voter->name}", ['voter_id' => $voter->id]);
        return redirect()->route('voters.index')->with('status', 'Data pemilih diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voter $voter)
    {
        $voter->is_active = false;
        $voter->save();
        AuditLogger::log('voter_deactivate', "Pemilih dinonaktifkan: {$voter->name}", ['voter_id' => $voter->id]);

        return redirect()->route('voters.index')->with('status', 'Pemilih dinonaktifkan.');
    }
}
