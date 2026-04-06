<?php

namespace Database\Seeders;

use App\Models\RoleType;
use Illuminate\Database\Seeder;

class RoleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'code' => 'client',
                'name' => 'Cliente',
                'description' => 'Usuario que solicita servicios de asistencia vial.',
                'is_active' => true,
            ],
            [
                'code' => 'provider',
                'name' => 'Proveedor',
                'description' => 'Usuario proveedor que atiende solicitudes de asistencia.',
                'is_active' => true,
            ],
            [
                'code' => 'admin',
                'name' => 'Administrador',
                'description' => 'Usuario administrativo con acceso al panel de gestión.',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            RoleType::updateOrCreate(
                ['code' => $role['code']],
                $role
            );
        }
    }
}