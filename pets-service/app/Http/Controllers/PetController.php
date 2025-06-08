<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
    /**
     * Obtener mascotas del usuario autenticado
     */
    public function index(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        $userRole = $request->attributes->get('user_role');

        $query = Pet::with(['species', 'breed']);

        // Solo administradores y veterinarios pueden ver todas las mascotas
        if (!in_array($userRole, ['administrador', 'veterinario'])) {
            $query->byOwner($userId);
        }

        // Filtros opcionales
        if ($request->has('owner_id') && in_array($userRole, ['administrador', 'veterinario'])) {
            $query->byOwner($request->owner_id);
        }

        if ($request->has('species_id')) {
            $query->bySpecies($request->species_id);
        }

        $pets = $query->active()->orderBy('name')->get();

        // Agregar edad calculada
        $pets->each(function ($pet) {
            $pet->age = $pet->age;
        });

        return response()->json([
            'success' => true,
            'data' => $pets
        ]);
    }

    /**
     * Registrar nueva mascota (RF-03)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'species_id' => 'required|exists:species,id',
            'breed_id' => 'required|exists:breeds,id',
            'birth_date' => 'required|date|before_or_equal:today',
            'weight' => 'nullable|numeric|min:0|max:999.99',
            'sex' => 'required|in:macho,hembra',
            'color' => 'nullable|string|max:100',
            'distinctive_marks' => 'nullable|string|max:500',
            'owner_id' => 'nullable|integer' // Solo admin/vet pueden asignar otro owner
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->attributes->get('user_id');
        $userRole = $request->attributes->get('user_role');

        // Determinar el propietario
        $ownerId = $userId; // Por defecto, el usuario autenticado
        if ($request->has('owner_id') && in_array($userRole, ['administrador', 'veterinario'])) {
            $ownerId = $request->owner_id;
        }

        $pet = Pet::create([
            'name' => $request->name,
            'species_id' => $request->species_id,
            'breed_id' => $request->breed_id,
            'birth_date' => $request->birth_date,
            'weight' => $request->weight,
            'sex' => $request->sex,
            'color' => $request->color,
            'distinctive_marks' => $request->distinctive_marks,
            'owner_id' => $ownerId
        ]);

        $pet->load(['species', 'breed']);
        $pet->age = $pet->age;

        return response()->json([
            'success' => true,
            'message' => 'Mascota registrada exitosamente',
            'data' => $pet
        ], 201);
    }

    /**
     * Obtener una mascota específica
     */
    public function show(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $userRole = $request->attributes->get('user_role');

        $pet = Pet::with(['species', 'breed'])->find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada'
            ], 404);
        }

        // Verificar permisos
        if (!in_array($userRole, ['administrador', 'veterinario']) && $pet->owner_id != $userId) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver esta mascota'
            ], 403);
        }

        $pet->age = $pet->age;

        return response()->json([
            'success' => true,
            'data' => $pet
        ]);
    }

    /**
     * Actualizar mascota
     */
    public function update(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $userRole = $request->attributes->get('user_role');

        $pet = Pet::find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada'
            ], 404);
        }

        // Verificar permisos
        if (!in_array($userRole, ['administrador', 'veterinario']) && $pet->owner_id != $userId) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar esta mascota'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'species_id' => 'sometimes|required|exists:species,id',
            'breed_id' => 'sometimes|required|exists:breeds,id',
            'birth_date' => 'sometimes|required|date|before_or_equal:today',
            'weight' => 'sometimes|nullable|numeric|min:0|max:999.99',
            'sex' => 'sometimes|required|in:macho,hembra',
            'color' => 'sometimes|nullable|string|max:100',
            'distinctive_marks' => 'sometimes|nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $pet->update($request->only([
            'name', 'species_id', 'breed_id', 'birth_date', 
            'weight', 'sex', 'color', 'distinctive_marks'
        ]));

        $pet->load(['species', 'breed']);
        $pet->age = $pet->age;

        return response()->json([
            'success' => true,
            'message' => 'Mascota actualizada exitosamente',
            'data' => $pet
        ]);
    }

    /**
     * Eliminar mascota (soft delete)
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $userRole = $request->attributes->get('user_role');

        $pet = Pet::find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada'
            ], 404);
        }

        // Solo admin puede eliminar
        if ($userRole !== 'administrador') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar mascotas'
            ], 403);
        }

        $pet->update(['active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Mascota eliminada exitosamente'
        ]);
    }

    /**
 * Resumen de mascota para otros microservicios
 */
public function summary(Request $request, $id)
{
    $userId = $request->attributes->get('user_id');
    $userRole = $request->attributes->get('user_role');

    $pet = Pet::with(['species', 'breed'])->find($id);

    if (!$pet) {
        return response()->json([
            'success' => false,
            'message' => 'Mascota no encontrada'
        ], 404);
    }

    // Verificar permisos
    if (!in_array($userRole, ['administrador', 'veterinario']) && $pet->owner_id != $userId) {
        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos para ver esta mascota'
        ], 403);
    }

    // Información resumida para otros servicios
    $summary = [
        'id' => $pet->id,
        'name' => $pet->name,
        'species' => $pet->species->name,
        'breed' => $pet->breed->name,
        'age' => $pet->age,
        'sex' => $pet->sex,
        'owner_id' => $pet->owner_id
    ];

    return response()->json([
        'success' => true,
        'data' => $summary
    ]);
}
}