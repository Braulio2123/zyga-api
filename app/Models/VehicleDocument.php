<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDocument extends Model
{
    protected $table = 'vehicle_documents';

    protected $fillable = [
        'vehicle_id',
        'document_type',
        'document_url',
    ];

    protected $casts = [
        'vehicle_id' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
