<?php

namespace Database\Seeders;

use App\Models\PaymentMethodType;
use Illuminate\Database\Seeder;

class PaymentMethodTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'card', 'name' => 'Tarjeta', 'is_active' => true],
            ['code' => 'cash', 'name' => 'Efectivo', 'is_active' => true],
            ['code' => 'transfer', 'name' => 'Transferencia', 'is_active' => true],
            ['code' => 'digital', 'name' => 'Digital', 'is_active' => true],
        ];

        foreach ($types as $type) {
            PaymentMethodType::updateOrCreate(
                ['code' => $type['code']],
                $type,
            );
        }
    }
}
