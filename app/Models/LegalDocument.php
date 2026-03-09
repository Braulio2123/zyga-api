<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalDocument extends Model
{
    protected $table = 'legal_documents';

    protected $fillable = [
        'consent_type_id',
        'version',
        'title',
        'body_text',
        'effective_from',
        'is_active',
    ];

    protected $casts = [
        'consent_type_id' => 'integer',
        'effective_from' => 'date',
        'is_active' => 'boolean',
    ];

    public function consentType(): BelongsTo
    {
        return $this->belongsTo(ConsentType::class, 'consent_type_id');
    }

    public function userConsents(): HasMany
    {
        return $this->hasMany(UserConsent::class, 'legal_document_id');
    }
}
