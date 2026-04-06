<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'code' => 'grua',
                'name' => 'Grúa',
                'description' => 'Servicio de arrastre o traslado del vehículo.',
                'is_active' => true,
            ],
            [
                'code' => 'paso_corriente',
                'name' => 'Paso de corriente',
                'description' => 'Asistencia por batería descargada.',
                'is_active' => true,
            ],
            [
                'code' => 'cambio_llanta',
                'name' => 'Cambio de llanta',
                'description' => 'Apoyo para reemplazar una llanta ponchada.',
                'is_active' => true,
            ],
            [
                'code' => 'envio_gasolina',
                'name' => 'Envío de gasolina',
                'description' => 'Suministro básico de combustible por emergencia.',
                'is_active' => true,
            ],
            [
                'code' => 'cerrajeria',
                'name' => 'Cerrajería vehicular',
                'description' => 'Apoyo en caso de llaves olvidadas dentro del vehículo o bloqueo.',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['code' => $service['code']],
                $service
            );
        }
    }
}