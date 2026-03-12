<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'request_id',
        'status_id',
        'payment_method_type_id',
        'payment_gateway_type_id',
        'amount_cents',
        'currency',
        'idempotency_key',
        'gateway_reference',
    ];

    protected $casts = [
        'request_id' => 'integer',
        'status_id' => 'integer',
        'payment_method_type_id' => 'integer',
        'payment_gateway_type_id' => 'integer',
        'amount_cents' => 'integer',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusType::class, 'status_id');
    }

    public function paymentMethodType(): BelongsTo
    {
        return $this->belongsTo(PaymentMethodType::class, 'payment_method_type_id');
    }

    public function paymentGatewayType(): BelongsTo
    {
        return $this->belongsTo(PaymentGatewayType::class, 'payment_gateway_type_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'payment_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'payment_id');
    }
}
