<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        // Spatie memiliki fungsi withCount bawaan untuk relasi users dan permissions
        $roles = Role::withCount(['users', 'permissions'])
                     ->orderByDesc('is_system')
                     ->orderBy('name')
                     ->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $groupedPermissions = Permission::getGroupedPermissions();
        return view('roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $validated['name'] = Str::slug($validated['label']);

        if (Role::where('name', $validated['name'])->exists()) {
            return back()->withInput()->with('error', 'Role dengan nama tersebut sudah ada.');
        }

        // 1. Buat Role-nya
        $role = Role::create([
            'name' => $validated['name'],
            'label' => $validated['label'],
            'description' => $validated['description'],
            'is_system' => 0 // Default bukan system
        ]);
        
        // 2. OTOMATIS SIMPAN KE TABEL `role_has_permissions` (Gaya Spatie)
        if (!empty($validated['permission_ids'])) {
            // Ubah array string ["9", "10"] menjadi array integer [9, 10]
            $permissionIds = array_map('intval', $validated['permission_ids']);
            
            $role->syncPermissions($permissionIds); 
        }

        AuditLogger::log('role_create', "Menambah role baru: {$role->name}");

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('roles.index')->with('error', 'Role sistem tidak boleh diedit.');
        }

        $groupedPermissions = Permission::getGroupedPermissions();
        
        // Kita ambil daftar ID permission yang sudah dimiliki untuk dicentang di frontend
        $rolePermissionIds = $role->permissions->pluck('id')->toArray(); 

        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('roles.index')->with('error', 'Role sistem tidak bisa diedit.');
        }

        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permission_ids' => 'nullable|array',
        ]);

        $validated['name'] = Str::slug($validated['label']);

        if (Role::where('name', $validated['name'])->where('id', '!=', $role->id)->exists()) {
            return back()->withInput()->with('error', 'Nama role sudah digunakan.');
        }

        // 1. Update data dasar role
        $role->update([
            'name' => $validated['name'],
            'label' => $validated['label'],
            'description' => $validated['description']
        ]);
        
        // 2. OTOMATIS UPDATE TABEL `role_has_permissions`
        // Jika checkbox kosong, array empty, syncPermissions otomatis menghapus semua akses lamanya
        $permissionIds = !empty($validated['permission_ids']) 
            ? array_map('intval', $validated['permission_ids']) 
            : [];
            
        $role->syncPermissions($permissionIds);

        AuditLogger::log('role_update', "Memperbarui role ID {$role->id}: {$role->name}");

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('roles.index')->with('error', 'Role system tidak boleh dihapus.');
        }

        // Spatie menggunakan tabel model_has_roles untuk mengecek apakah role masih dipakai user
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Role tidak boleh dihapus karena masih digunakan oleh pengguna.');
        }

        AuditLogger::log('role_delete', "Menghapus role ID {$role->id}: {$role->name}");
        
        // Saat $role->delete() dijalankan, Spatie OTOMATIS menghapus datanya dari role_has_permissions juga!
        $role->delete(); 

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
