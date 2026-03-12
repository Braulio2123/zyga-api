<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceToken extends Model
{
    protected $table = 'device_tokens';

    protected $fillable = [
        'user_id',
        'platform_type_id',
        'token',
        'last_seen_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'platform_type_id' => 'integer',
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function platformType(): BelongsTo
    {
        return $this->belongsTo(DevicePlatformType::class, 'platform_type_id');
    }
}
