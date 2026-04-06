<?php

namespace Database\Seeders;

use App\Models\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $clientRole = RoleType::query()->where('code', 'client')->firstOrFail();
        $providerRole = RoleType::query()->where('code', 'provider')->firstOrFail();
        $adminRole = RoleType::query()->where('code', 'admin')->firstOrFail();

        $client = User::updateOrCreate(
            ['email' => 'client@zyga.com'],
            ['password' => Hash::make('password123')]
        );

        $provider = User::updateOrCreate(
            ['email' => 'provider@zyga.com'],
            ['password' => Hash::make('password123')]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@zyga.com'],
            ['password' => Hash::make('password123')]
        );

        $client->roles()->sync([$clientRole->id]);
        $provider->roles()->sync([$providerRole->id]);
        $admin->roles()->sync([$adminRole->id]);
    }
}