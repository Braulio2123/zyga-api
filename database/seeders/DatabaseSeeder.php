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
            StatusDomainSeeder::class,
            StatusTypeSeeder::class,
            UserSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}