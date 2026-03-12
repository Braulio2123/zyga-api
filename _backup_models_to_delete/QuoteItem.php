<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $table = 'quote_items';

    protected $fillable = [
        'quote_id',
        'code',
        'description',
        'amount_cents',
        'sort_order',
    ];

    protected $casts = [
        'quote_id' => 'integer',
        'amount_cents' => 'integer',
        'sort_order' => 'integer',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }
}
