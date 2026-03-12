<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProviderService extends Pivot
{
    protected $table = 'provider_services';

    public $timestamps = true;

    protected $fillable = [
        'provider_id',
        'service_id',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'service_id' => 'integer',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
