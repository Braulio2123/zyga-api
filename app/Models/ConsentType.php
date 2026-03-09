<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsentType extends Catalog
{
    protected $table = 'consent_types';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    public function legalDocuments(): HasMany
    {
        return $this->hasMany(LegalDocument::class, 'consent_type_id');
    }
}
