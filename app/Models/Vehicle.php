<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        'user_id',
        'vehicle_type_id',
        'plate',
        'brand',
        'model',
        'year',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'vehicle_type_id' => 'integer',
        'year' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function assistanceRequests(): HasMany
    {
        return $this->hasMany(AssistanceRequest::class, 'vehicle_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'vehicles_services',
            'vehicle_id',
            'service_id'
        )->withTimestamps();
    }
}
