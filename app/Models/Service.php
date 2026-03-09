<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Catalog
{
    protected $table = 'services';

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(
            Provider::class,
            'provider_services',
            'service_id',
            'provider_id'
        )->withTimestamps();
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(
            Vehicle::class,
            'vehicles_services',
            'service_id',
            'vehicle_id'
        )->withTimestamps();
    }

    public function assistanceRequests(): HasMany
    {
        return $this->hasMany(AssistanceRequest::class, 'service_id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'service_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class, 'service_id');
    }
}
