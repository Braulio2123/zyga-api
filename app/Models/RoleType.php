<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleType extends Catalog
{
    use HasFactory;

    protected $table = 'role_types';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'role_id',
            'user_id'
        )->withTimestamps();
    }
}
