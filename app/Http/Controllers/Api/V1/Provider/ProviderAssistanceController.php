<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Provider;
use App\Models\RequestEvent;
use App\Models\RequestHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderAssistanceController extends Controller
{
    protected function resolveProvider(Request $request): ?Provider
    {
        return Provider::query()
            ->with('services')
            ->where('user_id', $request->user()->id)
            ->first();
    }

    public function available(Request $request): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $serviceIds = $provider->services->pluck('id')->all();

        if (empty($serviceIds)) {
            return response()->json([
                'message' => 'El proveedor no tiene servicios asociados.',
                'data' => [],
            ], 200);
        }

        $requests = AssistanceRequest::query()
            ->with(['service', 'vehicle', 'user'])
            ->whereNull('provider_id')
            ->where('status', 'created')
            ->whereIn('service_id', $serviceIds)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Solicitudes disponibles obtenidas correctamente.',
            'data' => $requests,
        ], 200);
    }

    public function index(Request $request): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $requests = AssistanceRequest::query()
            ->with(['service', 'vehicle', 'user'])
            ->where('provider_id', $provider->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Solicitudes del proveedor obtenidas correctamente.',
            'data' => $requests,
        ], 200);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $assistanceRequest = AssistanceRequest::query()
            ->with(['service', 'vehicle', 'user', 'history', 'events'])
            ->where('provider_id', $provider->id)
            ->where('id', $id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud no encontrada para el proveedor autenticado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Solicitud del proveedor obtenida correctamente.',
            'data' => $assistanceRequest,
        ], 200);
    }

    public function accept(Request $request, int $id): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $serviceIds = $provider->services->pluck('id')->all();

        if (empty($serviceIds)) {
            return response()->json([
                'message' => 'El proveedor no tiene servicios asociados.',
            ], 422);
        }

        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $id)
            ->whereNull('provider_id')
            ->where('status', 'created')
            ->whereIn('service_id', $serviceIds)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'La solicitud no está disponible para ser aceptada por este proveedor.',
            ], 404);
        }

        DB::transaction(function () use ($assistanceRequest, $provider) {
            $assistanceRequest->provider_id = $provider->id;
            $assistanceRequest->status = 'assigned';
            $assistanceRequest->save();

            RequestHistory::create([
                'request_id' => $assistanceRequest->id,
                'status' => 'assigned',
            ]);

            RequestEvent::create([
                'request_id' => $assistanceRequest->id,
                'event_type' => 'provider_accepted',
                'event_data' => [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->display_name,
                ],
            ]);
        });

        $assistanceRequest->load(['service', 'vehicle', 'user', 'history', 'events']);

        return response()->json([
            'message' => 'Solicitud aceptada correctamente por el proveedor.',
            'data' => $assistanceRequest,
        ], 200);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $assistanceRequest = AssistanceRequest::query()
            ->where('provider_id', $provider->id)
            ->where('id', $id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud no encontrada para el proveedor autenticado.',
            ], 404);
        }

        $data = $request->validate([
            'status' => ['required', 'string', 'in:in_progress,completed,cancelled'],
        ]);

        $currentStatus = $assistanceRequest->status;
        $newStatus = $data['status'];

        $allowedTransitions = [
            'assigned' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
        ];

        if (
            !isset($allowedTransitions[$currentStatus]) ||
            !in_array($newStatus, $allowedTransitions[$currentStatus], true)
        ) {
            return response()->json([
                'message' => "No se permite cambiar el estado de {$currentStatus} a {$newStatus}.",
            ], 422);
        }

        DB::transaction(function () use ($assistanceRequest, $newStatus, $provider) {
            $assistanceRequest->status = $newStatus;
            $assistanceRequest->save();

            RequestHistory::create([
                'request_id' => $assistanceRequest->id,
                'status' => $newStatus,
            ]);

            RequestEvent::create([
                'request_id' => $assistanceRequest->id,
                'event_type' => 'provider_status_updated',
                'event_data' => [
                    'provider_id' => $provider->id,
                    'new_status' => $newStatus,
                ],
            ]);
        });

        $assistanceRequest->load(['service', 'vehicle', 'user', 'history', 'events']);

        return response()->json([
            'message' => 'Estado de la solicitud actualizado correctamente.',
            'data' => $assistanceRequest,
        ], 200);
    }
}