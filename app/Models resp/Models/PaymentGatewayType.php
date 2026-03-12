<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGatewayType extends Catalog
{
    protected $table = 'payment_gateway_types';

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_gateway_type_id');
    }
}
