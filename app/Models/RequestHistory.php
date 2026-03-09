<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestHistory extends Model
{
    protected $table = 'request_history';

    protected $fillable = [
        'request_id',
        'version_no',
        'snapshot',
    ];

    protected $casts = [
        'request_id' => 'integer',
        'version_no' => 'integer',
        'snapshot' => 'array',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }
}
