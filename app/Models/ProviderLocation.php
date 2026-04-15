<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderLocation extends Model
{
    protected $table = 'provider_locations';

    protected $fillable = [
        'provider_id',
        'assistance_request_id',
        'lat',
        'lng',
        'accuracy',
        'heading',
        'speed',
        'recorded_at',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'assistance_request_id' => 'integer',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'heading' => 'decimal:2',
        'speed' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function assistanceRequest(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'assistance_request_id');
    }
}
