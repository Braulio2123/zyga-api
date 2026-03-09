<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalDocument extends Model
{
    protected $table = 'legal_documents';

    protected $fillable = [
        'consent_type_id',
        'version',
        'published_at',
        'content_hash',
        'is_active',
    ];

    protected $casts = [
        'consent_type_id' => 'integer',
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function consentType(): BelongsTo
    {
        return $this->belongsTo(ConsentType::class, 'consent_type_id');
    }
}
