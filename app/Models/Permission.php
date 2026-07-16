<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'guard_name', 'label', 'group_name', 'description'];

    // Tetap pertahankan fungsi grouping Anda untuk mempermudah di View
    public static function getGroupedPermissions()
    {
        return self::orderBy('group_name')->orderBy('label')->get()->groupBy(function($data) {
            return $data->group_name ?: 'Lainnya';
        });
    }
}
