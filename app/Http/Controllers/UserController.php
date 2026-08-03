<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;
use App\Models\Region;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Services\AuditLogger;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /* public function index()
    {
        $users = User::with('roles', 'organization', 'region')->paginate(15);
        return view('users.index', compact('users'));
    } */
   public function index()
    {
        $query = User::with('roles', 'organization', 'region');

        if (! auth()->user()->hasRole('superadmin')) {
            $query->where('region_id', auth()->user()->region_id);
        }

        $users = $query->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $roles = Role::all();

        // Data Scoping: Menyaring Organisasi dan Wilayah berdasarkan Role
        if ($user->hasRole('superadmin')) {
            $organizations = Organization::where('is_active', true)->get();
            // PERBAIKAN: Hapus orderBy('level') dan tambahkan 'parent_id'
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
                })->orderBy('name')->get(['id', 'organization_id', 'parent_id', 'name', 'level']); // Tambahkan parent_id
        }

        return view('users.create', compact('organizations', 'regions', 'roles', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $validated['organization_id'],
            'region_id' => $validated['region_id'],
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);
        AuditLogger::log('user_create', "Akun petugas dibuat: {$user->username}");
        return redirect()->route('users.index')->with('status', 'Akun petugas berhasil dibuat.');
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
    public function edit(Request $request, User $user)
    {
        $authUser = $request->user(); // Yang sedang login
        $roles = Role::all();

        // Data Scoping identik dengan fungsi create
        if ($user->hasRole('superadmin')) {
            $organizations = Organization::where('is_active', true)->get();
            // PERBAIKAN: Hapus orderBy('level') dan tambahkan 'parent_id'
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
                })->orderBy('name')->get(['id', 'organization_id', 'parent_id', 'name', 'level']); // Tambahkan parent_id
        }

        return view('users.edit', compact('user', 'organizations', 'regions', 'roles', 'authUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->name = $validated['name'];
        //$user->username = $validated['username'];
        $user->organization_id = $validated['organization_id'];
        $user->region_id = $validated['region_id'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);
        AuditLogger::log('user_update', "Data petugas diperbarui: {$user->username}");
        return redirect()->route('users.index')->with('status', 'Data petugas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Sengaja TIDAK dihapus permanen dari database -- demi jejak audit.
        // Cukup dinonaktifkan, supaya riwayat aktivitas user ini (misal token yang pernah diterbitkan) tetap tertelusuri.
        $user->is_active = false;
        $user->save();
        AuditLogger::log('user_deactivate', "Akun dinonaktifkan: {$user->username}");
        return redirect()->route('users.index')->with('status', 'Akun petugas dinonaktifkan.');
    }

    public function activate(User $user)
    {
        $user->is_active = true;
        $user->save();
        AuditLogger::log('user_activate', "Akun diaktifkan kembali: {$user->username}");
        return redirect()->route('users.index')->with('status', 'Akun petugas diaktifkan kembali.');
    }
}
