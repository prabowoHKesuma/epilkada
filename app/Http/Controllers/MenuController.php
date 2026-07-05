<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
        return view('menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parents = Menu::whereNull('parent_id')->get();
        $roles = Role::all();
        return view('menus.create', compact('parents', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'menu_key' => ['required', 'string', 'max:100', 'unique:menus,menu_key'],
            'url' => ['nullable', 'string', 'max:255'],
            'icon_class' => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:menus,id'],
            'sort_order' => ['nullable', 'integer'],
            'role_ids' => ['nullable', 'array'],
        ]);

        $menu = Menu::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'menu_key' => $validated['menu_key'],
            'title' => $validated['title'],
            'url' => $validated['url'] ?? null,
            'icon_class' => $validated['icon_class'] ?? 'fas fa-circle',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        foreach ($validated['role_ids'] ?? [] as $roleId) {
            RoleMenu::create(['role_id' => $roleId, 'menu_id' => $menu->id]);
        }

        return redirect()->route('menus.index')->with('status', 'Menu berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $parents = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)->get();
        $roles = Role::all();
        $selectedRoleIds = $menu->roleMenus()->pluck('role_id')->toArray();

        return view('menus.edit', compact('menu', 'parents', 'roles', 'selectedRoleIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'menu_key' => ['required', 'string', 'max:100', 'unique:menus,menu_key,'.$menu->id],
            'url' => ['nullable', 'string', 'max:255'],
            'icon_class' => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:menus,id'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'role_ids' => ['nullable', 'array'],
        ]);

        $menu->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'menu_key' => $validated['menu_key'],
            'title' => $validated['title'],
            'url' => $validated['url'] ?? null,
            'icon_class' => $validated['icon_class'] ?? 'fas fa-circle',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        RoleMenu::where('menu_id', $menu->id)->delete();
        foreach ($validated['role_ids'] ?? [] as $roleId) {
            RoleMenu::create(['role_id' => $roleId, 'menu_id' => $menu->id]);
        }

        return redirect()->route('menus.index')->with('status', 'Menu berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        abort_if($menu->children()->count() > 0, 403, 'Hapus dulu semua sub-menu di bawahnya sebelum menghapus menu ini.');

        RoleMenu::where('menu_id', $menu->id)->delete();
        $menu->delete();

        return redirect()->route('menus.index')->with('status', 'Menu dihapus.');
    }
}
