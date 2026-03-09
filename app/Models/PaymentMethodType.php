<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethodType extends Catalog
{
    protected $table = 'payment_method_types';

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_method_type_id');
    }
}
