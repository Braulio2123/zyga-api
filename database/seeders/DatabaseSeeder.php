<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleTypeSeeder::class,
            VehicleTypeSeeder::class,
            ServiceSeeder::class,
            ServiceVehicleRateSeeder::class,
            StatusDomainSeeder::class,
            StatusTypeSeeder::class,
            PaymentMethodTypeSeeder::class,
            UserSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
