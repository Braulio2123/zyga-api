<?php

namespace Database\Seeders;

use App\Models\AssistanceRequest;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\ProviderDocument;
use App\Models\ProviderSchedule;
use App\Models\RequestEvent;
use App\Models\RequestHistory;
use App\Models\RoleType;
use App\Models\Service;
use App\Models\StatusType;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $client = User::query()->where('email', 'client@zyga.com')->firstOrFail();
        $providerUser = User::query()->where('email', 'provider@zyga.com')->firstOrFail();
        $admin = User::query()->where('email', 'admin@zyga.com')->firstOrFail();

        $clientRole = RoleType::query()->where('code', 'client')->firstOrFail();
        $providerRole = RoleType::query()->where('code', 'provider')->firstOrFail();
        $adminRole = RoleType::query()->where('code', 'admin')->firstOrFail();

        $client->roles()->sync([$clientRole->id]);
        $providerUser->roles()->sync([$providerRole->id]);
        $admin->roles()->sync([$adminRole->id]);

        $providerStatus = StatusType::query()
            ->where('code', 'active')
            ->firstOrFail();

        $provider = Provider::updateOrCreate(
            ['user_id' => $providerUser->id],
            [
                'display_name' => 'Grúas Express GDL',
                'provider_kind' => 'grua',
                'status_id' => $providerStatus->id,
                'is_verified' => true,
            ]
        );

        $services = Service::query()
            ->whereIn('code', ['grua', 'paso_corriente', 'cambio_llanta'])
            ->pluck('id')
            ->all();

        if (!empty($services)) {
            $provider->services()->sync($services);
        }

        ProviderSchedule::updateOrCreate(
            [
                'provider_id' => $provider->id,
                'day_of_week' => 1,
            ],
            [
                'start_time' => '08:00:00',
                'end_time' => '18:00:00',
                'timezone' => 'America/Mexico_City',
                'is_active' => true,
            ]
        );

        ProviderDocument::updateOrCreate(
            [
                'provider_id' => $provider->id,
                'document_type' => 'licencia',
            ],
            [
                'document_url' => 'https://example.com/documentos/licencia-gdl.pdf',
            ]
        );

        $vehicleType = VehicleType::query()->where('code', 'auto')->firstOrFail();

        $vehicle = Vehicle::updateOrCreate(
            ['plate' => 'JAL457B'],
            [
                'user_id' => $client->id,
                'vehicle_type_id' => $vehicleType->id,
                'brand' => 'Nissan',
                'model' => 'Versa',
                'year' => 2020,
            ]
        );

        UserAddress::updateOrCreate(
            [
                'user_id' => $client->id,
                'address' => 'Av. Vallarta 1450',
            ],
            [
                'city' => 'Guadalajara',
                'state' => 'Jalisco',
                'country' => 'México',
                'zip_code' => '44100',
            ]
        );

        $towService = Service::query()->where('code', 'grua')->firstOrFail();
        $batteryService = Service::query()->where('code', 'paso_corriente')->firstOrFail();

        $availableRequest = AssistanceRequest::updateOrCreate(
            ['public_id' => 'ZYGAREQAVAILABLE0000000001'],
            [
                'user_id' => $client->id,
                'provider_id' => null,
                'service_id' => $towService->id,
                'vehicle_id' => $vehicle->id,
                'lat' => 20.67360000,
                'lng' => -103.34400000,
                'pickup_address' => 'Av. Juárez 123, Guadalajara, Jalisco',
                'status' => 'created',
            ]
        );

        $completedRequest = AssistanceRequest::updateOrCreate(
            ['public_id' => 'ZYGAREQCOMPLETED0000000001'],
            [
                'user_id' => $client->id,
                'provider_id' => $provider->id,
                'service_id' => $batteryService->id,
                'vehicle_id' => $vehicle->id,
                'lat' => 20.67000000,
                'lng' => -103.35000000,
                'pickup_address' => 'Av. México 2500, Guadalajara, Jalisco',
                'status' => 'completed',
            ]
        );

        RequestHistory::updateOrCreate(
            [
                'request_id' => $availableRequest->id,
                'status' => 'created',
            ],
            []
        );

        RequestEvent::updateOrCreate(
            [
                'request_id' => $availableRequest->id,
                'event_type' => 'request_created',
            ],
            [
                'event_data' => [
                    'source' => 'seeder',
                    'status' => 'created',
                ],
            ]
        );

        foreach (['created', 'assigned', 'in_progress', 'completed'] as $index => $status) {
            RequestHistory::updateOrCreate(
                [
                    'request_id' => $completedRequest->id,
                    'status' => $status,
                ],
                []
            );

            RequestEvent::updateOrCreate(
                [
                    'request_id' => $completedRequest->id,
                    'event_type' => 'status_' . $status,
                ],
                [
                    'event_data' => [
                        'source' => 'seeder',
                        'status' => $status,
                        'step' => $index + 1,
                    ],
                ]
            );
        }

        Notification::updateOrCreate(
            [
                'user_id' => $client->id,
                'type' => 'assistance_request',
                'message' => 'Tu solicitud de asistencia fue registrada correctamente.',
            ],
            [
                'is_read' => false,
            ]
        );

        Notification::updateOrCreate(
            [
                'user_id' => $client->id,
                'type' => 'payment',
                'message' => 'Tu pago fue registrado correctamente.',
            ],
            [
                'is_read' => false,
            ]
        );

        Payment::updateOrCreate(
            [
                'transaction_id' => 'TXN-ZYGA-DEMO-0001',
            ],
            [
                'assistance_request_id' => $completedRequest->id,
                'amount' => 850.00,
                'payment_method' => 'card',
                'status' => 'completed',
            ]
        );
    }
}