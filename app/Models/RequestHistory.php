<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestHistory extends Model
{
    protected $table = 'request_history';

    protected $fillable = [
        'request_id',
        'status',
    ];

    protected $casts = [
        'request_id' => 'integer',
    ];

    public function assistanceRequest(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }
}
