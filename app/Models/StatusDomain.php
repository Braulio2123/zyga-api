<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusDomain extends Model
{
    protected $table = 'status_domains';

    protected $fillable = [
        'code',
        'name',
    ];

    public function statusTypes(): HasMany
    {
        return $this->hasMany(StatusType::class, 'domain_id');
    }
}
