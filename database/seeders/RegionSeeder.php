<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org = Organization::firstOrCreate(
            ['name' => 'Kelurahan Sukun'],
            ['type' => 'kelurahan', 'is_active' => true]
        );

        $kelurahan = Region::firstOrCreate(
            ['code' => 'KEL-SUKUN'],
            ['organization_id' => $org->id, 'parent_id' => null, 'level' => 'kelurahan', 'name' => 'Sukun']
        );

        // GANTI daftar ini sesuai jumlah RW riil di Kelurahan Sukun
        // (tanyakan ke kantor kelurahan kalau belum tahu persis jumlahnya)
        $rwList = [
            ['code' => 'RW004', 'name' => 'RW 004'],
            ['code' => 'RW010', 'name' => 'RW 010'],
        ];

        foreach ($rwList as $rw) {
            $rwRegion = Region::firstOrCreate(
                ['code' => $rw['code']],
                ['organization_id' => $org->id, 'parent_id' => $kelurahan->id, 'level' => 'rw', 'name' => $rw['name']]
            );

            // Contoh RT di bawah tiap RW -- sesuaikan juga jumlahnya
            for ($i = 1; $i <= 13; $i++) {
                Region::firstOrCreate(
                    ['code' => $rw['code'].'-RT0'.$i],
                    ['organization_id' => $org->id, 'parent_id' => $rwRegion->id, 'level' => 'rt', 'name' => 'RT 0'.$i]
                );
            }
        }
    }
}
