<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusType extends Model
{
    use HasFactory;

    protected $table = 'status_types';

    protected $fillable = [
        'domain_id',
        'code',
        'name',
        'description',
        'is_terminal',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'domain_id' => 'integer',
        'is_terminal' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(StatusDomain::class, 'domain_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'status_id');
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'status_id');
    }

    public function assistanceRequests(): HasMany
    {
        return $this->hasMany(AssistanceRequest::class, 'current_status_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'status_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'status_id');
    }

    public function providerDocuments(): HasMany
    {
        return $this->hasMany(ProviderDocument::class, 'status_id');
    }
}
