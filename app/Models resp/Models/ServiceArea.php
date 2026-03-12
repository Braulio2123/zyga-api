<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceArea extends Model
{
    protected $table = 'service_areas';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function polygons(): HasMany
    {
        return $this->hasMany(ServiceAreaPolygon::class, 'service_area_id');
    }

    public function assistanceRequests(): HasMany
    {
        return $this->hasMany(AssistanceRequest::class, 'service_area_id');
    }
}
