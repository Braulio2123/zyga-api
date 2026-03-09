<?php

namespace App\Models;

class PaymentMethodType extends Catalog
{
    protected $table = 'payment_method_types';

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];
}
