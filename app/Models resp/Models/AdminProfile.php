<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProfile extends Model
{
    protected $table = 'admin_profiles';

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'can_manage_catalogs',
        'can_assign_requests',
        'is_super_admin',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'can_manage_catalogs' => 'boolean',
        'can_assign_requests' => 'boolean',
        'is_super_admin' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
