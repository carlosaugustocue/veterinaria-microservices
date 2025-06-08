<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use Illuminate\Http\Request;

class BreedController extends Controller
{
    /**
     * Obtener todas las razas activas
     */
    public function index(Request $request)
    {
        $query = Breed::active()->with('species');

        // Filtrar por especie si se proporciona
        if ($request->has('species_id')) {
            $query->bySpecies($request->species_id);
        }

        $breeds = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $breeds
        ]);
    }

    /**
     * Obtener razas por especie
     */
    public function bySpecies($speciesId)
    {
        $breeds = Breed::active()
            ->bySpecies($speciesId)
            ->orderBy('name')
            ->get(['id', 'name', 'species_id']);

        return response()->json([
            'success' => true,
            'data' => $breeds
        ]);
    }

    /**
     * Obtener una raza específica
     */
    public function show($id)
    {
        $breed = Breed::with('species')->find($id);

        if (!$breed) {
            return response()->json([
                'success' => false,
                'message' => 'Raza no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $breed
        ]);
    }
}