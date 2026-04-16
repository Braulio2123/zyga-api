<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceVehicleRate;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class ServiceVehicleRateSeeder extends Seeder
{
    public function run(): void
    {
        $matrix = [
            'grua' => [
                'auto' => ['base_amount' => 900, 'night_surcharge' => 180, 'weekend_surcharge' => 120],
                'moto' => ['base_amount' => 650, 'night_surcharge' => 130, 'weekend_surcharge' => 90],
                'camioneta' => ['base_amount' => 1100, 'night_surcharge' => 220, 'weekend_surcharge' => 150],
                'pickup' => ['base_amount' => 1200, 'night_surcharge' => 240, 'weekend_surcharge' => 160],
                'suv' => ['base_amount' => 1050, 'night_surcharge' => 210, 'weekend_surcharge' => 140],
            ],
            'paso_corriente' => [
                'auto' => ['base_amount' => 280, 'night_surcharge' => 60, 'weekend_surcharge' => 40],
                'moto' => ['base_amount' => 220, 'night_surcharge' => 50, 'weekend_surcharge' => 30],
                'camioneta' => ['base_amount' => 320, 'night_surcharge' => 70, 'weekend_surcharge' => 50],
                'pickup' => ['base_amount' => 340, 'night_surcharge' => 75, 'weekend_surcharge' => 55],
                'suv' => ['base_amount' => 330, 'night_surcharge' => 70, 'weekend_surcharge' => 50],
            ],
            'cambio_llanta' => [
                'auto' => ['base_amount' => 320, 'night_surcharge' => 70, 'weekend_surcharge' => 50],
                'moto' => ['base_amount' => 260, 'night_surcharge' => 55, 'weekend_surcharge' => 35],
                'camioneta' => ['base_amount' => 380, 'night_surcharge' => 80, 'weekend_surcharge' => 60],
                'pickup' => ['base_amount' => 400, 'night_surcharge' => 85, 'weekend_surcharge' => 65],
                'suv' => ['base_amount' => 390, 'night_surcharge' => 80, 'weekend_surcharge' => 60],
            ],
            'envio_gasolina' => [
                'auto' => ['base_amount' => 260, 'night_surcharge' => 50, 'weekend_surcharge' => 35],
                'moto' => ['base_amount' => 220, 'night_surcharge' => 45, 'weekend_surcharge' => 30],
                'camioneta' => ['base_amount' => 300, 'night_surcharge' => 60, 'weekend_surcharge' => 45],
                'pickup' => ['base_amount' => 320, 'night_surcharge' => 65, 'weekend_surcharge' => 50],
                'suv' => ['base_amount' => 310, 'night_surcharge' => 60, 'weekend_surcharge' => 45],
            ],
            'cerrajeria' => [
                'auto' => ['base_amount' => 350, 'night_surcharge' => 80, 'weekend_surcharge' => 55],
                'moto' => ['base_amount' => 300, 'night_surcharge' => 65, 'weekend_surcharge' => 45],
                'camioneta' => ['base_amount' => 420, 'night_surcharge' => 90, 'weekend_surcharge' => 65],
                'pickup' => ['base_amount' => 440, 'night_surcharge' => 95, 'weekend_surcharge' => 70],
                'suv' => ['base_amount' => 430, 'night_surcharge' => 90, 'weekend_surcharge' => 65],
            ],
        ];

        foreach ($matrix as $serviceCode => $vehicleRates) {
            $service = Service::query()->where('code', $serviceCode)->firstOrFail();

            foreach ($vehicleRates as $vehicleTypeCode => $rateData) {
                $vehicleType = VehicleType::query()->where('code', $vehicleTypeCode)->firstOrFail();

                ServiceVehicleRate::updateOrCreate(
                    [
                        'service_id' => $service->id,
                        'vehicle_type_id' => $vehicleType->id,
                    ],
                    [
                        'base_amount' => $rateData['base_amount'],
                        'night_surcharge' => $rateData['night_surcharge'],
                        'weekend_surcharge' => $rateData['weekend_surcharge'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
