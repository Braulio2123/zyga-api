<?php

namespace Database\Seeders;

use App\Models\StatusDomain;
use App\Models\StatusType;
use Illuminate\Database\Seeder;

class StatusTypeSeeder extends Seeder
{
    public function run(): void
    {
        $providerDomain = StatusDomain::query()->where('code', 'provider')->firstOrFail();

        $statuses = [
            [
                'domain_id' => $providerDomain->id,
                'code' => 'active',
                'name' => 'Activo',
                'description' => 'Proveedor activo y disponible para operar.',
                'is_terminal' => false,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'domain_id' => $providerDomain->id,
                'code' => 'pending',
                'name' => 'Pendiente',
                'description' => 'Proveedor pendiente de validación o configuración.',
                'is_terminal' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'domain_id' => $providerDomain->id,
                'code' => 'suspended',
                'name' => 'Suspendido',
                'description' => 'Proveedor suspendido temporalmente.',
                'is_terminal' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            StatusType::updateOrCreate(
                [
                    'domain_id' => $status['domain_id'],
                    'code' => $status['code'],
                ],
                $status
            );
        }
    }
}