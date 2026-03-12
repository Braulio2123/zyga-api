<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $table = 'attachments';

    protected $fillable = [
        'request_id',
        'user_id',
        'attachment_type_id',
        'storage_disk',
        'storage_path',
        'mime_type',
        'size_bytes',
        'checksum_sha256',
    ];

    protected $casts = [
        'request_id' => 'integer',
        'user_id' => 'integer',
        'attachment_type_id' => 'integer',
        'size_bytes' => 'integer',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachmentType(): BelongsTo
    {
        return $this->belongsTo(AttachmentType::class, 'attachment_type_id');
    }
}
