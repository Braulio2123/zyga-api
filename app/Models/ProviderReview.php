<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderReview extends Model
{
    protected $table = 'provider_reviews';

    protected $fillable = [
        'provider_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'rating' => 'integer',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
}
