<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Provider;
use App\Models\ProviderLocation;
use App\Support\AssistanceRequestFlow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProviderTrackingController extends Controller
{
    protected function resolveProvider(Request $request): ?Provider
    {
        return Provider::query()
            ->where('user_id', $request->user()->id)
            ->first();
    }

    public function store(Request $request): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $data = $request->validate([
            'assistance_request_id' => ['required', 'integer', 'exists:assistance_requests,id'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'between:0,360'],
            'speed' => ['nullable', 'numeric', 'min:0'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $data['assistance_request_id'])
            ->where('provider_id', $provider->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'La solicitud no pertenece al proveedor autenticado.',
            ], 403);
        }

        if (!in_array($assistanceRequest->status, [
            AssistanceRequestFlow::ASSIGNED,
            AssistanceRequestFlow::IN_PROGRESS,
        ], true)) {
            return response()->json([
                'message' => 'La ubicación solo puede actualizarse cuando la solicitud está asignada o en progreso.',
                'data' => [
                    'current_status' => $assistanceRequest->status,
                ],
            ], 422);
        }

        $location = ProviderLocation::create([
            'provider_id' => $provider->id,
            'assistance_request_id' => $assistanceRequest->id,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'accuracy' => $data['accuracy'] ?? null,
            'heading' => $data['heading'] ?? null,
            'speed' => $data['speed'] ?? null,
            'recorded_at' => isset($data['recorded_at'])
                ? Carbon::parse($data['recorded_at'])
                : now(),
        ]);

        return response()->json([
            'message' => 'Ubicación del proveedor registrada correctamente.',
            'data' => [
                'id' => $location->id,
                'provider_id' => $location->provider_id,
                'assistance_request_id' => $location->assistance_request_id,
                'lat' => $location->lat,
                'lng' => $location->lng,
                'accuracy' => $location->accuracy,
                'heading' => $location->heading,
                'speed' => $location->speed,
                'recorded_at' => optional($location->recorded_at)?->toISOString(),
                'created_at' => optional($location->created_at)?->toISOString(),
            ],
        ], 201);
    }
}
