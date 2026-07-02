<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage_region',
            'manage_user',
            'manage_election',
            'manage_candidate',
            'manage_voter',
            'issue_tps_token',
            'process_voting',
            'verify_remote_voter',
            'view_result',
            'view_audit_log',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Role sesuai kebutuhan uji coba Kelurahan Sukun
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin->syncPermissions(Permission::all()); // superadmin dapat semua akses

        $adminKelurahan = Role::firstOrCreate(['name' => 'admin_kelurahan']);
        $adminKelurahan->syncPermissions([
            'manage_election', 'manage_candidate', 'manage_voter',
            'issue_tps_token', 'view_result', 'view_audit_log',
        ]);

        $petugasTps = Role::firstOrCreate(['name' => 'petugas_tps']);
        $petugasTps->syncPermissions([
            'issue_tps_token', 'process_voting',
        ]);
    }
}
