<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Catalog
{
    protected $table = 'vehicle_types';

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_type_id');
    }
}
