<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceVehicleRate extends Model
{
    protected $table = 'service_vehicle_rates';

    protected $fillable = [
        'service_id',
        'vehicle_type_id',
        'base_amount',
        'night_surcharge',
        'weekend_surcharge',
        'is_active',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'vehicle_type_id' => 'integer',
        'base_amount' => 'decimal:2',
        'night_surcharge' => 'decimal:2',
        'weekend_surcharge' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }
}
