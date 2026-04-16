<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceVehicleRate;
use App\Models\Vehicle;
use Carbon\CarbonInterface;

class SimplePricingService
{
    public function quote(Service $service, Vehicle $vehicle, ?CarbonInterface $moment = null): ?array
    {
        $moment = $moment ?? now();

        $rate = ServiceVehicleRate::query()
            ->where('service_id', $service->id)
            ->where('vehicle_type_id', $vehicle->vehicle_type_id)
            ->where('is_active', true)
            ->first();

        if (!$rate) {
            return null;
        }

        $isNight = $moment->hour >= 20 || $moment->hour < 6;
        $isWeekend = $moment->isWeekend();

        $baseAmount = (float) $rate->base_amount;
        $nightSurcharge = $isNight ? (float) $rate->night_surcharge : 0.00;
        $weekendSurcharge = $isWeekend ? (float) $rate->weekend_surcharge : 0.00;
        $quotedAmount = $baseAmount + $nightSurcharge + $weekendSurcharge;

        return [
            'service_id' => $service->id,
            'vehicle_id' => $vehicle->id,
            'vehicle_type_id' => $vehicle->vehicle_type_id,
            'currency' => 'MXN',
            'base_amount' => round($baseAmount, 2),
            'night_surcharge' => round($nightSurcharge, 2),
            'weekend_surcharge' => round($weekendSurcharge, 2),
            'quoted_amount' => round($quotedAmount, 2),
            'applied_rate_id' => $rate->id,
            'conditions' => [
                'is_night' => $isNight,
                'is_weekend' => $isWeekend,
            ],
        ];
    }
}
