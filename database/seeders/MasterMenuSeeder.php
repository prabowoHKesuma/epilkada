<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\RoleMenu;

class MasterMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadminID = 1;

        $menus = [
            [
                'parent_id' => null,
                'menu_key' => 'dashboard',
                'title' => 'Dashboard',
                'url' => '/dashboard',
                'icon_class' => 'bi bi-speedometer2',
                'group_name' => 'Main',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'parent_id' => null,
                'menu_key' => 'voters',
                'title' => 'Pemilih',
                'url' => '/voters',
                'icon_class' => 'bi bi-people-fill',
                'group_name' => 'Main',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'parent_id' => null,
                'menu_key' => 'regions',
                'title' => 'Wilayah',
                'url' => '/regions',
                'icon_class' => 'bi bi-geo-alt-fill',
                'group_name' => 'Main',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
