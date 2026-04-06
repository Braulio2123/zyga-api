<?php

namespace Database\Seeders;

use App\Models\StatusDomain;
use Illuminate\Database\Seeder;

class StatusDomainSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            [
                'code' => 'provider',
                'name' => 'Estatus de proveedor',
            ],
        ];

        foreach ($domains as $domain) {
            StatusDomain::updateOrCreate(
                ['code' => $domain['code']],
                $domain
            );
        }
    }
}