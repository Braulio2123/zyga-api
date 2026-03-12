<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestAssignment extends Model
{
    protected $table = 'request_assignments';

    protected $fillable = [
        'request_id',
        'provider_id',
        'assigned_by',
        'assigned_at',
    ];

    protected $casts = [
        'request_id' => 'integer',
        'provider_id' => 'integer',
        'assigned_by' => 'integer',
        'assigned_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
