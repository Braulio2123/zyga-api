<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $table = 'quotes';

    protected $fillable = [
        'request_id',
        'pricing_rule_id',
        'total_cents',
        'currency',
    ];

    protected $casts = [
        'request_id' => 'integer',
        'pricing_rule_id' => 'integer',
        'total_cents' => 'integer',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }

    public function pricingRule(): BelongsTo
    {
        return $this->belongsTo(PricingRule::class, 'pricing_rule_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class, 'quote_id');
    }
}
