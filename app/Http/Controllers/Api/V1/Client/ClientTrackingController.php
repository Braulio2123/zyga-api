<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientTrackingController extends Controller
{
    public function show(Request $request, string $id): JsonResponse
    {
        $assistanceRequest = AssistanceRequest::query()
            ->with([
                'service',
                'vehicle',
                'provider.user',
                'latestProviderLocation',
            ])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud de asistencia no encontrada.',
            ], 404);
        }

        $provider = $assistanceRequest->provider;
        $latestLocation = $assistanceRequest->latestProviderLocation;

        return response()->json([
            'message' => 'Seguimiento de la solicitud obtenido correctamente.',
            'data' => [
                'request' => [
                    'id' => $assistanceRequest->id,
                    'public_id' => $assistanceRequest->public_id,
                    'status' => $assistanceRequest->status,
                    'cancel_reason' => $assistanceRequest->cancel_reason,
                    'pickup_address' => $assistanceRequest->pickup_address,
                    'pickup_reference' => $assistanceRequest->pickup_reference,
                    'lat' => $assistanceRequest->lat,
                    'lng' => $assistanceRequest->lng,
                    'service' => $assistanceRequest->service ? [
                        'id' => $assistanceRequest->service->id,
                        'name' => $assistanceRequest->service->name ?? null,
                    ] : null,
                    'vehicle' => $assistanceRequest->vehicle ? [
                        'id' => $assistanceRequest->vehicle->id,
                        'brand' => $assistanceRequest->vehicle->brand ?? null,
                        'model' => $assistanceRequest->vehicle->model ?? null,
                        'plate' => $assistanceRequest->vehicle->plate ?? null,
                        'color' => $assistanceRequest->vehicle->color ?? null,
                    ] : null,
                ],
                'provider' => $provider ? [
                    'id' => $provider->id,
                    'user_id' => $provider->user_id,
                    'display_name' => $provider->display_name,
                    'provider_kind' => $provider->provider_kind,
                    'email' => $provider->user?->email,
                ] : null,
                'provider_location' => $latestLocation ? [
                    'id' => $latestLocation->id,
                    'lat' => $latestLocation->lat,
                    'lng' => $latestLocation->lng,
                    'accuracy' => $latestLocation->accuracy,
                    'heading' => $latestLocation->heading,
                    'speed' => $latestLocation->speed,
                    'recorded_at' => optional($latestLocation->recorded_at)?->toISOString(),
                    'created_at' => optional($latestLocation->created_at)?->toISOString(),
                ] : null,
                'meta' => [
                    'has_provider_assigned' => !is_null($assistanceRequest->provider_id),
                    'has_provider_location' => !is_null($latestLocation),
                ],
            ],
        ], 200);
    }
}
