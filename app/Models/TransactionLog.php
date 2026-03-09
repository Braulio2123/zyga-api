<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    protected $table = 'transaction_logs';

    protected $fillable = [
        'transaction_id',
        'action',
        'description',
    ];

    protected $casts = [
        'transaction_id' => 'integer',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
