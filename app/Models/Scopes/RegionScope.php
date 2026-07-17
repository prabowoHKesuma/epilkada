<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\Region;

class RegionScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        // Superadmin bebas lihat semua wilayah, tidak difilter
        if ($user->hasRole('superadmin')) {
            return;
        }

        // 2. Filter Hierarki untuk User (Kelurahan, RW, dll)
        if ($user->region_id) {
            // Ambil ID wilayah user sendiri dan semua ID turunan di bawahnya
            $allowedRegionIds = $this->getDescendantRegionIds($user->region_id);
            
            // Masukkan juga ID wilayah user itu sendiri ke dalam daftar
            $allowedRegionIds[] = $user->region_id; 

            // Gunakan whereIn agar bisa menampilkan miliknya DAN milik bawahannya
            $builder->whereIn('region_id', $allowedRegionIds);
        } else {
            // Jika user biasa tapi tidak punya region_id, amankan dengan tidak menampilkan apa-apa
            $builder->whereNull('region_id');
        }
    }

    private function getDescendantRegionIds($parentId): array
    {
        // Cari ID wilayah yang atasan langsungnya adalah $parentId
        $childIds = Region::where('parent_id', $parentId)->pluck('id')->toArray();
        
        $allIds = $childIds;
        
        // Cek lagi ke bawah, apakah anak-anak ini punya bawahan lagi?
        foreach ($childIds as $childId) {
            $allIds = array_merge($allIds, $this->getDescendantRegionIds($childId));
        }

        return $allIds;
    }
}
