<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAreaPolygon extends Model
{
    protected $table = 'service_area_polygons';

    protected $fillable = [
        'service_area_id',
        'polygon',
    ];

    protected $casts = [
        'service_area_id' => 'integer',
    ];

    public function serviceArea(): BelongsTo
    {
        return $this->belongsTo(ServiceArea::class, 'service_area_id');
    }
}
