<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssistanceRequest extends Model
{
    protected $table = 'assistance_requests';

    protected $fillable = [
        'public_id',
        'user_id',
        'provider_id',
        'service_id',
        'vehicle_id',
        'lat',
        'lng',
        'pickup_address',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'provider_id' => 'integer',
        'service_id' => 'integer',
        'vehicle_id' => 'integer',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(RequestEvent::class, 'request_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(RequestHistory::class, 'request_id');
    }

    public function quote(): HasOne
    {
        return $this->hasOne(Quote::class, 'request_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'request_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'request_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'request_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'request_id');
    }

    public function providerPayouts(): HasMany
    {
        return $this->hasMany(ProviderPayout::class, 'request_id');
    }
}