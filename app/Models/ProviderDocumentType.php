<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderDocumentType extends Catalog
{
    protected $table = 'provider_document_types';

    public function providerDocuments(): HasMany
    {
        return $this->hasMany(ProviderDocument::class, 'document_type_id');
    }
}
