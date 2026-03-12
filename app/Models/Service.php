<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
}
