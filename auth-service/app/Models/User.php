<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\Hash;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'nombre',
        'apellido', 
        'email',
        'password',
        'telefono',
        'role_id',
        'active'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'locked_until' => 'datetime',
        'active' => 'boolean'
    ];

    /**
     * Relación con rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Mutator para encriptar password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Verificar si la cuenta está bloqueada
     */
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Bloquear cuenta por intentos fallidos
     */
    public function lockAccount()
    {
        $this->update([
            'locked_until' => now()->addMinutes(15),
            'failed_login_attempts' => 0
        ]);
    }

    /**
     * Incrementar intentos fallidos
     */
    public function incrementFailedAttempts()
    {
        $attempts = $this->failed_login_attempts + 1;
        
        if ($attempts >= 5) {
            $this->lockAccount();
        } else {
            $this->update(['failed_login_attempts' => $attempts]);
        }
    }

    /**
     * Resetear intentos fallidos
     */
    public function resetFailedAttempts()
    {
        $this->update(['failed_login_attempts' => 0]);
    }

    /**
     * Obtener nombre completo
     */
    public function getFullNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    /**
     * Verificar si tiene rol específico
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }
}