<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderPayout extends Model
{
    protected $table = 'provider_payouts';

    protected $fillable = [
        'provider_id',
        'request_id',
        'status_id',
        'amount_cents',
        'currency',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'request_id' => 'integer',
        'status_id' => 'integer',
        'amount_cents' => 'integer',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusType::class, 'status_id');
    }
}
