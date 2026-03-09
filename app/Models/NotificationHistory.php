<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationHistory extends Model
{
    protected $table = 'notifications_history';

    protected $fillable = [
        'notification_id',
        'status',
        'read_at',
    ];

    protected $casts = [
        'notification_id' => 'integer',
        'read_at' => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
