<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'assistance_request_id',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'transaction_id',
        'status',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'assistance_request_id' => 'integer',
        'amount' => 'decimal:2',
        'validated_by' => 'integer',
        'validated_at' => 'datetime',
    ];

    public function assistanceRequest(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'assistance_request_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'payment_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
