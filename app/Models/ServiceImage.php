<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceImage extends Model
{
    protected $table = 'service_images';

    protected $fillable = [
        'service_id',
        'image_url',
        'description',
    ];

    protected $casts = [
        'service_id' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
