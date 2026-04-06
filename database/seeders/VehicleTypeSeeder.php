<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $vehicleTypes = [
            ['code' => 'auto', 'name' => 'Automóvil', 'is_active' => true],
            ['code' => 'moto', 'name' => 'Motocicleta', 'is_active' => true],
            ['code' => 'camioneta', 'name' => 'Camioneta', 'is_active' => true],
            ['code' => 'pickup', 'name' => 'Pickup', 'is_active' => true],
            ['code' => 'suv', 'name' => 'SUV', 'is_active' => true],
        ];

        foreach ($vehicleTypes as $vehicleType) {
            VehicleType::updateOrCreate(
                ['code' => $vehicleType['code']],
                $vehicleType
            );
        }
    }
}