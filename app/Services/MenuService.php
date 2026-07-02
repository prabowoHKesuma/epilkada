<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getMenuForCurrentUser()
    {
        if (! Auth::check()) {
            return collect();
        }

        $roleIds = Auth::user()->roles->pluck('id');

        return Menu::whereNull('parent_id')
            ->whereHas('roleMenus', fn ($q) => $q->whereIn('role_id', $roleIds))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['children' => function ($q) use ($roleIds) {
                $q->whereHas('roleMenus', fn ($q2) => $q2->whereIn('role_id', $roleIds))
                  ->where('is_active', true)
                  ->orderBy('sort_order');
            }])
            ->get();
    }
}
