<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Species;

class SpeciesSeeder extends Seeder
{
    public function run()
    {
        $species = [
            ['name' => 'Perro', 'description' => 'Canis lupus familiaris'],
            ['name' => 'Gato', 'description' => 'Felis catus'],
            ['name' => 'Ave', 'description' => 'Aves domésticas y de compañía'],
            ['name' => 'Conejo', 'description' => 'Oryctolagus cuniculus'],
            ['name' => 'Hámster', 'description' => 'Cricetinae'],
            ['name' => 'Reptil', 'description' => 'Reptiles de compañía']
        ];

        foreach ($species as $speciesData) {
            Species::firstOrCreate(
                ['name' => $speciesData['name']],
                $speciesData
            );
        }
    }
}