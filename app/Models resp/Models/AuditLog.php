<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'actor_user_id',
        'action',
        'entity_type',
        'entity_id',
        'ip',
        'user_agent',
        'payload',
    ];

    protected $casts = [
        'actor_user_id' => 'integer',
        'entity_id' => 'integer',
        'payload' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
