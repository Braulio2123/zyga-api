<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Provider;
use App\Services\AssistanceRequestLifecycleService;
use App\Support\AssistanceRequestFlow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderAssistanceController extends Controller
{
    public function __construct(
        protected AssistanceRequestLifecycleService $lifecycle,
    ) {
    }

    protected function resolveProvider(Request $request): ?Provider
    {
        return Provider::query()
            ->with(['services', 'status'])
            ->where('user_id', $request->user()->id)
            ->first();
    }

    protected function ensureProviderCanOperate(Provider $provider): ?JsonResponse
    {
        if (!$provider->is_verified) {
            return response()->json([
                'message' => 'El proveedor no puede operar hasta ser validado por administración.',
            ], 422);
        }

        if (optional($provider->status)->code !== 'active') {
            return response()->json([
                'message' => 'El proveedor no está en estado operativo activo.',
                'data' => [
                    'status' => optional($provider->status)->code,
                ],
            ], 422);
        }

        return null;
    }

    public function available(Request $request): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        if ($response = $this->ensureProviderCanOperate($provider)) {
            return $response;
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
            ->where('status', AssistanceRequestFlow::CREATED)
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

        if ($response = $this->ensureProviderCanOperate($provider)) {
            return $response;
        }

        $serviceIds = $provider->services->pluck('id')->all();

        if (empty($serviceIds)) {
            return response()->json([
                'message' => 'El proveedor no tiene servicios asociados.',
            ], 422);
        }

        $assistanceRequest = DB::transaction(function () use ($id, $provider, $serviceIds, $request) {
            $assistanceRequest = AssistanceRequest::query()
                ->where('id', $id)
                ->whereIn('service_id', $serviceIds)
                ->lockForUpdate()
                ->first();

            if (!$assistanceRequest || !AssistanceRequestFlow::providerCanAccept($assistanceRequest->status, $assistanceRequest->provider_id)) {
                return null;
            }

            $assistanceRequest->provider_id = $provider->id;
            $assistanceRequest->status = AssistanceRequestFlow::ASSIGNED;
            $assistanceRequest->save();

            $this->lifecycle->createTimelineEntry(
                $assistanceRequest,
                AssistanceRequestFlow::ASSIGNED,
                'provider_accepted',
                [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->display_name,
                    'status' => AssistanceRequestFlow::ASSIGNED,
                ]
            );

            $this->lifecycle->notifyUser(
                $assistanceRequest->user_id,
                'assistance_request',
                'Tu solicitud fue aceptada por un proveedor y ya quedó asignada.'
            );

            $this->lifecycle->notifyUser(
                $request->user()->id,
                'assistance_request',
                'Aceptaste correctamente una solicitud de asistencia.'
            );

            $this->lifecycle->audit(
                $request->user()->id,
                'provider.assistance_request.accepted',
                [
                    'assistance_request_id' => $assistanceRequest->id,
                    'public_id' => $assistanceRequest->public_id,
                    'provider_id' => $provider->id,
                ]
            );

            return $assistanceRequest;
        });

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'La solicitud ya no está disponible para ser aceptada por este proveedor.',
            ], 409);
        }

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

        if ($response = $this->ensureProviderCanOperate($provider)) {
            return $response;
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
            'status' => ['required', 'string', 'in:' . implode(',', [
                AssistanceRequestFlow::IN_PROGRESS,
                AssistanceRequestFlow::COMPLETED,
                AssistanceRequestFlow::CANCELLED,
            ])],
        ]);

        $currentStatus = $assistanceRequest->status;
        $newStatus = $data['status'];

        if (!AssistanceRequestFlow::canProviderTransition($currentStatus, $newStatus)) {
            return response()->json([
                'message' => "No se permite cambiar el estado de {$currentStatus} a {$newStatus}.",
            ], 422);
        }

        DB::transaction(function () use ($assistanceRequest, $currentStatus, $newStatus, $provider, $request) {
            $assistanceRequest->status = $newStatus;
            $assistanceRequest->save();

            $eventType = match ($newStatus) {
                AssistanceRequestFlow::IN_PROGRESS => 'provider_started_service',
                AssistanceRequestFlow::COMPLETED => 'provider_completed_service',
                AssistanceRequestFlow::CANCELLED => 'provider_cancelled_service',
                default => 'provider_status_updated',
            };

            $this->lifecycle->createTimelineEntry(
                $assistanceRequest,
                $newStatus,
                $eventType,
                [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->display_name,
                    'new_status' => $newStatus,
                ]
            );

            $clientMessage = match ($newStatus) {
                AssistanceRequestFlow::IN_PROGRESS => 'El proveedor reportó que el servicio ya está en proceso.',
                AssistanceRequestFlow::COMPLETED => 'El proveedor marcó el servicio como completado. Ya puedes registrar el pago.',
                AssistanceRequestFlow::CANCELLED => 'El proveedor canceló la atención de la solicitud.',
                default => 'La solicitud tuvo una actualización de estado.',
            };

            $this->lifecycle->notifyUser(
                $assistanceRequest->user_id,
                'assistance_request',
                $clientMessage
            );

            $this->lifecycle->audit(
                $request->user()->id,
                'provider.assistance_request.status_updated',
                [
                    'assistance_request_id' => $assistanceRequest->id,
                    'public_id' => $assistanceRequest->public_id,
                    'from' => $currentStatus,
                    'to' => $newStatus,
                    'provider_id' => $provider->id,
                ]
            );
        });

        $assistanceRequest->refresh()->load(['service', 'vehicle', 'user', 'history', 'events']);

        return response()->json([
            'message' => 'Estado de la solicitud actualizado correctamente.',
            'data' => $assistanceRequest,
        ], 200);
    }
}
