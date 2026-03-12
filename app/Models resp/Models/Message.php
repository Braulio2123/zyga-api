<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'request_id',
        'sender_user_id',
        'message_type_id',
        'template_id',
        'body_text',
        'payload',
    ];

    protected $casts = [
        'request_id' => 'integer',
        'sender_user_id' => 'integer',
        'message_type_id' => 'integer',
        'template_id' => 'integer',
        'payload' => 'array',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class, 'request_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function messageType(): BelongsTo
    {
        return $this->belongsTo(MessageType::class, 'message_type_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }
}
