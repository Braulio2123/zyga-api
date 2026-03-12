<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsentType extends Model
{
    protected $table = 'consent_types';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function legalDocuments(): HasMany
    {
        return $this->hasMany(LegalDocument::class, 'consent_type_id');
    }
}
