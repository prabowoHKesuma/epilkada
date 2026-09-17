<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;
use App\Models\Region;
use App\Models\Organization;
use App\Http\Requests\StoreVoterRequest;
use App\Services\PiiHasher;
use Illuminate\Validation\ValidationException;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;

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
    public function create(Request $request)
    {
        $user = $request->user();

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
        
        return view('voters.create', compact('organizations', 'regions', 'user'));
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
            'organization_id' => $validated['organization_id'], // PERBAIKAN: Ambil dari form, bukan auth()->user()
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
    public function edit(Request $request, Voter $voter)
    {
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
        
        return view('voters.edit', compact('voter', 'organizations', 'regions', 'authUser'));
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
            'organization_id' => $validated['organization_id'], // PERBAIKAN BILA DIEDIT
            'region_id' => $validated['region_id'],
            'name' => $validated['name'],
            'nik_hash' => $nikHash,
            'kk_hash' => PiiHasher::hash($validated['kk']),
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
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

    // =========================================================================
    // HELPER METHODS UNTUK HIERARKI REGION
    // =========================================================================

    /**
     * Mengambil daftar wilayah yang diizinkan untuk dikelola oleh user yang login.
     */
    private function getAllowedRegions()
    {
        $user = Auth::user();

        // Kita batasi hanya menampilkan level kelurahan, rw, dan rt di form Pendaftaran Pemilih
        $query = Region::whereIn('level', ['kelurahan', 'rw', 'rt']);

        // Jika dia bukan superadmin, kita filter sesuai wilayah kekuasaannya
        if (!$user->hasRole('superadmin')) {
            if ($user->region_id) {
                // Ambil ID-nya sendiri dan semua bawahannya (sama seperti di RegionScope)
                $allowedIds = $this->getDescendantRegionIds($user->region_id);
                $allowedIds[] = $user->region_id;

                $query->whereIn('id', $allowedIds);
            } else {
                // User biasa tanpa region_id = tidak bisa melihat pilihan wilayah apapun
                $query->whereNull('id');
            }
        }

        return $query->get();
    }

    /**
     * Fungsi rekursif untuk mencari ID anak, cucu, dst.
     */
    private function getDescendantRegionIds($parentId): array
    {
        $childIds = Region::where('parent_id', $parentId)->pluck('id')->toArray();
        $allIds = $childIds;
        
        foreach ($childIds as $childId) {
            $allIds = array_merge($allIds, $this->getDescendantRegionIds($childId));
        }

        return $allIds;
    }
}