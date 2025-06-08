<?php

namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\Request;

class SpeciesController extends Controller
{
    /**
     * Obtener todas las especies activas
     */
    public function index()
    {
        $species = Species::active()
            ->with(['breeds' => function($query) {
                $query->active();
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $species
        ]);
    }

    /**
     * Obtener especies sin razas
     */
    public function list()
    {
        $species = Species::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $species
        ]);
    }

    /**
     * Obtener una especie específica
     */
    public function show($id)
    {
        $species = Species::with(['breeds' => function($query) {
            $query->active();
        }])->find($id);

        if (!$species) {
            return response()->json([
                'success' => false,
                'message' => 'Especie no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $species
        ]);
    }
}