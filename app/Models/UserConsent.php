<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    protected $table = 'user_consents';

    protected $fillable = [
        'user_id',
        'legal_document_id',
        'accepted_at',
        'accepted_ip',
        'user_agent',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'legal_document_id' => 'integer',
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function legalDocument(): BelongsTo
    {
        return $this->belongsTo(LegalDocument::class, 'legal_document_id');
    }
}
