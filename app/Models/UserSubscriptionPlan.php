<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscriptionPlan extends Model
{
    protected $table = 'user_subscription_plans';

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'subscription_plan_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
