<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    protected $fillable = [
        'name',
        'species_id',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Relación con especie
     */
    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * Relación con mascotas
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * Scope para razas activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope por especie
     */
    public function scopeBySpecies($query, $speciesId)
    {
        return $query->where('species_id', $speciesId);
    }
}