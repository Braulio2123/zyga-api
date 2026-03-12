<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingRule extends Model
{
    protected $table = 'pricing_rules';

    protected $fillable = [
        'service_id',
        'vehicle_type_id',
        'base_fee_cents',
        'per_km_cents',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'vehicle_type_id' => 'integer',
        'base_fee_cents' => 'integer',
        'per_km_cents' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'pricing_rule_id');
    }
}
