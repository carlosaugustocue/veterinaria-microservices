<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Species;
use App\Models\Breed;

class BreedSeeder extends Seeder
{
    public function run()
    {
        // Obtener especies
        $perro = Species::where('name', 'Perro')->first();
        $gato = Species::where('name', 'Gato')->first();
        $ave = Species::where('name', 'Ave')->first();

        $breeds = [
            // Perros
            ['name' => 'Labrador Retriever', 'species_id' => $perro->id],
            ['name' => 'Golden Retriever', 'species_id' => $perro->id],
            ['name' => 'Pastor Alemán', 'species_id' => $perro->id],
            ['name' => 'Bulldog Francés', 'species_id' => $perro->id],
            ['name' => 'Chihuahua', 'species_id' => $perro->id],
            ['name' => 'Mestizo', 'species_id' => $perro->id],
            
            // Gatos
            ['name' => 'Persa', 'species_id' => $gato->id],
            ['name' => 'Siamés', 'species_id' => $gato->id],
            ['name' => 'Maine Coon', 'species_id' => $gato->id],
            ['name' => 'Británico de Pelo Corto', 'species_id' => $gato->id],
            ['name' => 'Doméstico', 'species_id' => $gato->id],
            
            // Aves
            ['name' => 'Canario', 'species_id' => $ave->id],
            ['name' => 'Periquito', 'species_id' => $ave->id],
            ['name' => 'Loro', 'species_id' => $ave->id],
        ];

        foreach ($breeds as $breedData) {
            Breed::firstOrCreate(
                ['name' => $breedData['name'], 'species_id' => $breedData['species_id']],
                $breedData
            );
        }
    }
}