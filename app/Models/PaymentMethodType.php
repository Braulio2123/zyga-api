<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodType extends Model
{
    protected $table = 'payment_method_types';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
