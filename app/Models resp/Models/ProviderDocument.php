<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderDocument extends Model
{
    protected $table = 'provider_documents';

    protected $fillable = [
        'provider_id',
        'document_type_id',
        'status_id',
        'storage_disk',
        'storage_path',
        'mime_type',
        'size_bytes',
        'checksum_sha256',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'document_type_id' => 'integer',
        'status_id' => 'integer',
        'size_bytes' => 'integer',
        'reviewed_by' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(ProviderDocumentType::class, 'document_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusType::class, 'status_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
