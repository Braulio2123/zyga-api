<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderLocationHistory extends Model
{
    protected $table = 'provider_location_history';

    protected $fillable = [
        'provider_id',
        'location',
        'accuracy_m',
        'recorded_at',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'accuracy_m' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
}
