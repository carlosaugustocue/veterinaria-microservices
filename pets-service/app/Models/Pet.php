<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pet extends Model
{
    protected $fillable = [
        'name',
        'species_id',
        'breed_id',
        'birth_date',
        'weight',
        'sex',
        'color',
        'distinctive_marks',
        'owner_id',
        'active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'decimal:2',
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
     * Relación con raza
     */
    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    /**
     * Calcular edad en años y meses
     */
    public function getAgeAttribute()
    {
        $now = Carbon::now();
        $birthDate = Carbon::parse($this->birth_date);
        
        $years = $birthDate->diffInYears($now);
        $months = $birthDate->copy()->addYears($years)->diffInMonths($now);
        
        return [
            'years' => $years,
            'months' => $months,
            'total_months' => $birthDate->diffInMonths($now),
            'human_readable' => $years > 0 ? 
                "{$years} año(s) y {$months} mes(es)" : 
                "{$months} mes(es)"
        ];
    }

    /**
     * Scope para mascotas activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope por propietario
     */
    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * Scope por especie
     */
    public function scopeBySpecies($query, $speciesId)
    {
        return $query->where('species_id', $speciesId);
    }
}