<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderLastLocation extends Model
{
    protected $table = 'provider_last_locations';

    protected $primaryKey = 'provider_id';
    public $incrementing = false;

    protected $fillable = [
        'provider_id',
        'location',
        'accuracy_m',
        'speed_kmh',
        'heading_deg',
        'recorded_at',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'accuracy_m' => 'integer',
        'speed_kmh' => 'integer',
        'heading_deg' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
}
