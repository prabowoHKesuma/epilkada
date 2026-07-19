<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuService
{
    public function getMenuForCurrentUser()
    {
        $user = Auth::user();

        // Jika user belum login (untuk berjaga-jaga)
        if (!$user) {
            return collect();
        }

        // 1. Ambil semua menu yang aktif, urutkan
        $allMenus = Menu::where('is_active', true)->orderBy('sort_order')->get();

        // 2. Pisahkan Induk dan Anak
        $parents = $allMenus->where('parent_id', null);
        $children = $allMenus->whereNotNull('parent_id')->groupBy('parent_id');

        $filteredSidebar = collect();

        foreach ($parents as $parent) {
            $allowedChildren = collect();

            // 3. Saring Anak Menu
            if (isset($children[$parent->id])) {
                foreach ($children[$parent->id] as $child) {
                    if (empty($child->permission_name)) {
                        $allowedChildren->push($child);
                    } else {
                        // KUNCI PERBAIKAN: Gunakan canAny() bawaan Laravel
                        $permissions = explode('|', $child->permission_name);
                        if ($user->canAny($permissions)) {
                            $allowedChildren->push($child);
                        }
                    }
                }
            }

            // 4. Saring Induk Menu
            $isSingleMenuAllowed = false;
            if (empty($parent->permission_name)) {
                $isSingleMenuAllowed = true;
            } else {
                $parentPermissions = explode('|', $parent->permission_name);
                // KUNCI PERBAIKAN: Gunakan canAny() bawaan Laravel
                $isSingleMenuAllowed = $user->canAny($parentPermissions);
            }

            $hasAllowedChildren = $allowedChildren->count() > 0;

            if ($parent->url === '#' || $parent->url === null) {
                // Jika dia punya sub-menu (Dropdown)
                if ($hasAllowedChildren) {
                    $parent->setRelation('children', $allowedChildren);
                    $filteredSidebar->push($parent);
                }
            } else {
                // Jika dia menu tunggal
                if ($isSingleMenuAllowed) {
                    $parent->setRelation('children', $allowedChildren);
                    $filteredSidebar->push($parent);
                }
            }
        }

        return $filteredSidebar;
    }
}