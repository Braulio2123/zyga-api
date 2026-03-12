<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationType extends Catalog
{
    protected $table = 'notification_types';

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notification_type_id');
    }
}
