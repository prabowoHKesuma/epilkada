<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;
use App\Http\Requests\StoreElectionRequest;
use App\Http\Requests\UpdateElectionRequest;
use App\Services\AuditLogger;
use App\Models\Organization;
use App\Models\Region;

class ElectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $elections = Election::withCount('candidates')->latest()->paginate(15);
        return view('elections.index', compact('elections'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        // Logika scoping persis seperti UserController
        if ($user->hasRole('superadmin')) {
            $organizations = Organization::where('is_active', true)->get();
            $regions = Region::orderBy('name')->get(['id', 'organization_id', 'parent_id', 'name', 'level']);
        } else {
            $organizations = Organization::where('id', $user->organization_id)->get();
            $regions = Region::where('organization_id', $user->organization_id)
                ->where(function($q) use ($user) {
                    $q->where('id', $user->region_id)
                      ->orWhere('parent_id', $user->region_id)
                      ->orWhereIn('parent_id', function($subQuery) use ($user) {
                          $subQuery->select('id')->from('regions')->where('parent_id', $user->region_id);
                      });
                })->orderBy('name')->get(['id', 'organization_id', 'parent_id', 'name', 'level']);
        }

        return view('elections.create', compact('organizations', 'regions', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreElectionRequest $request)
    {
        $validated = $request->validated();

        Election::create([
            // AMBIL DARI FORM VALIDATED, BUKAN DARI AUTH USER (Kecuali created_by)
            'organization_id' => $validated['organization_id'],
            'region_id' => $validated['region_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'draft',
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('elections.index')->with('status', 'Pemilihan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Election $election)
    {
        $election->load('candidates');
        return view('elections.show', compact('election'));
    }

    public function edit(Request $request, Election $election)
    {
        $this->ensureEditable($election);
        $authUser = $request->user();

        if ($authUser->hasRole('superadmin')) {
            $organizations = Organization::where('is_active', true)->get();
            $regions = Region::orderBy('name')->get(['id', 'organization_id', 'parent_id', 'name', 'level']);
        } else {
            $organizations = Organization::where('id', $authUser->organization_id)->get();
            $regions = Region::where('organization_id', $authUser->organization_id)
                ->where(function($q) use ($authUser) {
                    $q->where('id', $authUser->region_id)
                      ->orWhere('parent_id', $authUser->region_id)
                      ->orWhereIn('parent_id', function($subQuery) use ($authUser) {
                          $subQuery->select('id')->from('regions')->where('parent_id', $authUser->region_id);
                      });
                })->orderBy('name')->get(['id', 'organization_id', 'parent_id', 'name', 'level']);
        }

        return view('elections.edit', compact('election', 'organizations', 'regions', 'authUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateElectionRequest $request, Election $election)
    {
        $this->ensureEditable($election);
        $election->update($request->validated());
        return redirect()->route('elections.index')->with('status', 'Pemilihan berhasil diperbarui.');
    }

    public function destroy(Election $election)
    {
        $this->ensureEditable($election);
        $election->delete();
        return redirect()->route('elections.index')->with('status', 'Pemilihan dihapus.');
    }

    public function publish(Election $election)
    {
        if ($election->status !== 'draft') {
            return back()->withErrors(['status' => 'Hanya pemilihan berstatus draft yang bisa dibuka.']);
        }

        if ($election->candidates()->count() < 2) {
            return back()->withErrors(['status' => 'Minimal harus ada 2 kandidat sebelum pemilihan dibuka.']);
        }

        $election->update(['status' => 'open']);
        AuditLogger::log('election_publish', "Pemilihan '{$election->title}' dibuka.", ['election_id' => $election->id]);
        return back()->with('status', 'Pemilihan dibuka. Pemungutan suara sudah bisa dimulai.');
    }

    public function close(Election $election)
    {
        if ($election->status !== 'open') {
            return back()->withErrors(['status' => 'Hanya pemilihan berstatus open yang bisa ditutup.']);
        }

        $election->update(['status' => 'closed']);
        AuditLogger::log('election_close', "Pemilihan '{$election->title}' ditutup.", ['election_id' => $election->id]);
        return back()->with('status', 'Pemungutan suara ditutup.');
    }

    public function finish(Election $election)
    {
        if ($election->status !== 'closed') {
            return back()->withErrors(['status' => 'Hanya pemilihan berstatus closed yang bisa diselesaikan.']);
        }

        $election->update(['status' => 'finished']);
        AuditLogger::log('election_finish', "Pemilihan '{$election->title}' difinalisasi.", ['election_id' => $election->id]);
        return back()->with('status', 'Pemilihan diselesaikan. Hasil sudah final.');
    }

    // --- Helper privat ---

    private function ensureEditable(Election $election): void
    {
        abort_if($election->status !== 'draft', 403, 'Pemilihan yang sudah dibuka tidak bisa diedit lagi, demi menjaga integritas data.');
    }
}
