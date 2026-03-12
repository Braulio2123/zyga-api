<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusType extends Model
{
    protected $table = 'status_types';

    protected $fillable = [
        'domain_id',
        'code',
        'name',
        'description',
        'sort_order',
        'is_terminal',
        'is_active',
    ];

    protected $casts = [
        'domain_id' => 'integer',
        'sort_order' => 'integer',
        'is_terminal' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(StatusDomain::class, 'domain_id');
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'status_id');
    }
}
