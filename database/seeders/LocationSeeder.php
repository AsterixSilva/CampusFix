<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $campus = Location::firstOrCreate([
            'name' => 'Universitas Negeri Medan',
            'type' => 'campus',
            'parent_id' => null,
        ]);

        $faculty = Location::firstOrCreate([
            'name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam',
            'type' => 'faculty',
            'parent_id' => $campus->id,
        ]);

        $building = Location::firstOrCreate([
            'name' => 'Gedung Lab Komputer',
            'type' => 'building',
            'parent_id' => $faculty->id,
        ]);

        $floor = Location::firstOrCreate([
            'name' => 'Lantai 1',
            'type' => 'floor',
            'parent_id' => $building->id,
        ]);

        Location::firstOrCreate([
            'name' => 'Ruang 101',
            'type' => 'room',
            'parent_id' => $floor->id,
        ]);

        Location::firstOrCreate([
            'name' => 'Area Koridor',
            'type' => 'area',
            'parent_id' => $floor->id,
        ]);
    }
}