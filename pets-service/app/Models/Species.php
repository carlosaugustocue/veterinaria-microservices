<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    protected $fillable = [
        'name',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Relación con razas
     */
    public function breeds()
    {
        return $this->hasMany(Breed::class);
    }

    /**
     * Relación con mascotas
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * Scope para especies activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}