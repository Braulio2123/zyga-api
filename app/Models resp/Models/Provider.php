<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Provider extends Model
{
    protected $table = 'providers';

    protected $fillable = [
        'user_id',
        'display_name',
        'provider_kind',
        'status_id',
        'is_verified',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'status_id' => 'integer',
        'is_verified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusType::class, 'status_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'provider_services',
            'provider_id',
            'service_id'
        )->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ProviderSchedule::class, 'provider_id');
    }

    public function lastLocation(): HasOne
    {
        return $this->hasOne(ProviderLastLocation::class, 'provider_id');
    }

    public function locationHistory(): HasMany
    {
        return $this->hasMany(ProviderLocationHistory::class, 'provider_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProviderDocument::class, 'provider_id');
    }

    public function assistanceRequests(): HasMany
    {
        return $this->hasMany(AssistanceRequest::class, 'provider_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RequestAssignment::class, 'provider_id');
    }
}
