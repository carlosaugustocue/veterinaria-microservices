<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name', 
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Relación con usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope para roles activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Verificar si es administrador
     */
    public function isAdmin()
    {
        return $this->name === 'administrador';
    }

    /**
     * Verificar si es veterinario
     */
    public function isVeterinario()
    {
        return $this->name === 'veterinario';
    }
}