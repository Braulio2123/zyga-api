<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequestFeedback extends Model
{
    protected $table = 'service_requests_feedback';

    protected $fillable = [
        'service_request_id',
        'rating',
        'feedback',
    ];

    protected $casts = [
        'service_request_id' => 'integer',
        'rating' => 'integer',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }
}
