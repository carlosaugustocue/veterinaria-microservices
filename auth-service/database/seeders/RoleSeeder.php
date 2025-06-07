<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'administrador',
                'display_name' => 'Administrador',
                'description' => 'Acceso completo al sistema, gestión de usuarios y configuración'
            ],
            [
                'name' => 'veterinario',
                'display_name' => 'Médico Veterinario',
                'description' => 'Puede realizar consultas, diagnósticos y gestionar historiales médicos'
            ],
            [
                'name' => 'recepcionista',
                'display_name' => 'Recepcionista',
                'description' => 'Gestión de citas, registro de clientes y mascotas'
            ],
            [
                'name' => 'auxiliar',
                'display_name' => 'Auxiliar Veterinario',
                'description' => 'Apoyo en consultas y procedimientos básicos'
            ]
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}