<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';

    protected $fillable = [
        'assistance_request_id',
        'service_id',
        'provider_id',
        'status',
    ];

    protected $casts = [
        'assistance_request_id' => 'integer',
        'service_id' => 'integer',
        'provider_id' => 'integer',
    ];

    public function assistanceRequest(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'assistance_request_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(ServiceHistory::class, 'service_request_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(ServiceFeedback::class, 'service_request_id');
    }

    public function requestFeedbacks(): HasMany
    {
        return $this->hasMany(ServiceRequestFeedback::class, 'service_request_id');
    }
}
