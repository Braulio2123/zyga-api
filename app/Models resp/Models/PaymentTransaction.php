<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $fillable = [
        'payment_id',
        'gateway_event_id',
        'event_type',
        'amount_cents',
        'raw_response',
    ];

    protected $casts = [
        'payment_id' => 'integer',
        'amount_cents' => 'integer',
        'raw_response' => 'array',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
