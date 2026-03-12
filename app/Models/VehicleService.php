<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class VehicleService extends Pivot
{
    protected $table = 'vehicles_services';

    public $timestamps = true;

    protected $fillable = [
        'vehicle_id',
        'service_id',
    ];

    protected $casts = [
        'vehicle_id' => 'integer',
        'service_id' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
