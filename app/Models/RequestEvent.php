<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestEvent extends Model
{
    protected $table = 'request_events';

    protected $fillable = [
        'request_id',
        'status_id',
        'actor_user_id',
        'event_code',
        'payload',
    ];

    protected $casts = [
        'request_id' => 'integer',
        'status_id' => 'integer',
        'actor_user_id' => 'integer',
        'payload' => 'array',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusType::class, 'status_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
