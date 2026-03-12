<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsentType extends Catalog
{
    protected $table = 'consent_types';

    public function legalDocuments(): HasMany
    {
        return $this->hasMany(LegalDocument::class, 'consent_type_id');
    }

    public function userConsents(): HasMany
    {
        return $this->hasMany(UserConsent::class, 'consent_type_id');
    }
}
