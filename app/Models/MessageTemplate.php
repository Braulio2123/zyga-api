<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageTemplate extends Model
{
    protected $table = 'message_templates';

    protected $fillable = [
        'code',
        'title',
        'body_text',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'template_id');
    }
}
