<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestEvent extends Model
{
    protected $table = 'request_events';

    protected $fillable = [
        'request_id',
        'event_type',
        'event_data',
    ];

    protected $casts = [
        'request_id' => 'integer',
        'event_data' => 'array',
    ];

    public function assistanceRequest(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }
}
