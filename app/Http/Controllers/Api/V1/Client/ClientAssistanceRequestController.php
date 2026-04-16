<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Service;
use App\Services\AssistanceRequestLifecycleService;
use App\Support\AssistanceRequestFlow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientAssistanceRequestController extends Controller
{
    public function __construct(
        protected AssistanceRequestLifecycleService $lifecycle,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $assistanceRequests = AssistanceRequest::query()
            ->with(['service', 'vehicle', 'provider'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Solicitudes de asistencia obtenidas correctamente.',
            'data' => $assistanceRequests,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'pickup_address' => ['required', 'string', 'max:255'],
            'pickup_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        $vehicle = $user->vehicles()
            ->where('id', $data['vehicle_id'])
            ->first();

        if (!$vehicle) {
            return response()->json([
                'message' => 'El vehículo seleccionado no pertenece al usuario autenticado.',
            ], 403);
        }

        $service = Service::query()
            ->where('id', $data['service_id'])
            ->where('is_active', true)
            ->first();

        if (!$service) {
            return response()->json([
                'message' => 'El servicio seleccionado no existe o no está activo.',
            ], 422);
        }

        $activeRequest = AssistanceRequest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', AssistanceRequestFlow::activeStatuses())
            ->first();

        if ($activeRequest) {
            return response()->json([
                'message' => 'Ya existe una solicitud activa para este usuario.',
                'data' => [
                    'active_request_id' => $activeRequest->id,
                    'active_request_public_id' => $activeRequest->public_id,
                    'active_request_status' => $activeRequest->status,
                ],
            ], 422);
        }

        $assistanceRequest = DB::transaction(function () use ($data, $service, $user) {
            $assistanceRequest = AssistanceRequest::create([
                'public_id' => Str::upper((string) Str::ulid()),
                'user_id' => $user->id,
                'provider_id' => null,
                'service_id' => $service->id,
                'vehicle_id' => $data['vehicle_id'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'pickup_address' => trim($data['pickup_address']),
                'pickup_reference' => filled($data['pickup_reference'] ?? null)
                    ? trim($data['pickup_reference'])
                    : null,
                'status' => AssistanceRequestFlow::CREATED,
            ]);

            $this->lifecycle->createTimelineEntry(
                $assistanceRequest,
                AssistanceRequestFlow::CREATED,
                'request_created',
                [
                    'message' => 'Solicitud creada por el cliente.',
                    'public_id' => $assistanceRequest->public_id,
                    'service_id' => $assistanceRequest->service_id,
                    'vehicle_id' => $assistanceRequest->vehicle_id,
                    'lat' => $assistanceRequest->lat,
                    'lng' => $assistanceRequest->lng,
                    'pickup_address' => $assistanceRequest->pickup_address,
                    'pickup_reference' => $assistanceRequest->pickup_reference,
                    'status' => $assistanceRequest->status,
                ]
            );

            $this->lifecycle->notifyUser(
                $user->id,
                'assistance_request',
                'Tu solicitud de asistencia fue registrada y está disponible para asignación.'
            );

            $this->lifecycle->audit(
                $user->id,
                'client.assistance_request.created',
                [
                    'assistance_request_id' => $assistanceRequest->id,
                    'public_id' => $assistanceRequest->public_id,
                    'service_id' => $assistanceRequest->service_id,
                    'vehicle_id' => $assistanceRequest->vehicle_id,
                    'pickup_reference' => $assistanceRequest->pickup_reference,
                ]
            );

            return $assistanceRequest;
        });

        $assistanceRequest->load(['service', 'vehicle', 'provider']);

        return response()->json([
            'message' => 'Solicitud de asistencia registrada correctamente.',
            'data' => $assistanceRequest,
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $assistanceRequest = AssistanceRequest::query()
            ->with(['service', 'vehicle', 'provider'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud de asistencia no encontrada.',
            ], 404);
        }

        return response()->json([
            'message' => 'Solicitud de asistencia obtenida correctamente.',
            'data' => $assistanceRequest,
        ], 200);
    }

    public function cancel(Request $request, string $id): JsonResponse
    {
        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud de asistencia no encontrada.',
            ], 404);
        }

        if (!AssistanceRequestFlow::clientCanCancel($assistanceRequest->status)) {
            return response()->json([
                'message' => 'La solicitud no puede cancelarse en su estado actual.',
                'data' => [
                    'current_status' => $assistanceRequest->status,
                ],
            ], 422);
        }

        $data = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($assistanceRequest, $data, $request) {
            $assistanceRequest->status = AssistanceRequestFlow::CANCELLED;
            $assistanceRequest->cancel_reason = trim($data['cancel_reason']);
            $assistanceRequest->save();

            $this->lifecycle->createTimelineEntry(
                $assistanceRequest,
                AssistanceRequestFlow::CANCELLED,
                'request_cancelled_by_client',
                [
                    'message' => 'Solicitud cancelada por el cliente.',
                    'cancel_reason' => $assistanceRequest->cancel_reason,
                    'status' => AssistanceRequestFlow::CANCELLED,
                ]
            );

            $this->lifecycle->notifyUser(
                $request->user()->id,
                'assistance_request',
                'Tu solicitud de asistencia fue cancelada correctamente.'
            );

            if (!is_null($assistanceRequest->provider?->user_id)) {
                $this->lifecycle->notifyUser(
                    $assistanceRequest->provider->user_id,
                    'assistance_request',
                    'Una solicitud asignada fue cancelada por el cliente.'
                );
            }

            $this->lifecycle->audit(
                $request->user()->id,
                'client.assistance_request.cancelled',
                [
                    'assistance_request_id' => $assistanceRequest->id,
                    'public_id' => $assistanceRequest->public_id,
                    'cancel_reason' => $assistanceRequest->cancel_reason,
                ]
            );
        });

        return response()->json([
            'message' => 'Solicitud de asistencia cancelada correctamente.',
            'data' => [
                'id' => $assistanceRequest->id,
                'public_id' => $assistanceRequest->public_id,
                'status' => $assistanceRequest->status,
                'cancel_reason' => $assistanceRequest->cancel_reason,
            ],
        ], 200);
    }

    public function status(Request $request, string $id): JsonResponse
    {
        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud de asistencia no encontrada.',
            ], 404);
        }

        return response()->json([
            'message' => 'Estado actual de la solicitud obtenido correctamente.',
            'data' => [
                'id' => $assistanceRequest->id,
                'public_id' => $assistanceRequest->public_id,
                'status' => $assistanceRequest->status,
                'provider_id' => $assistanceRequest->provider_id,
                'cancel_reason' => $assistanceRequest->cancel_reason,
                'pickup_address' => $assistanceRequest->pickup_address,
                'pickup_reference' => $assistanceRequest->pickup_reference,
            ],
        ], 200);
    }

    public function timeline(Request $request, string $id): JsonResponse
    {
        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud de asistencia no encontrada.',
            ], 404);
        }

        $history = $assistanceRequest->history()->orderBy('id')->get();
        $events = $assistanceRequest->events()->orderBy('id')->get();

        return response()->json([
            'message' => 'Línea de tiempo obtenida correctamente.',
            'data' => [
                'request' => [
                    'id' => $assistanceRequest->id,
                    'public_id' => $assistanceRequest->public_id,
                    'status' => $assistanceRequest->status,
                    'provider_id' => $assistanceRequest->provider_id,
                    'cancel_reason' => $assistanceRequest->cancel_reason,
                    'pickup_address' => $assistanceRequest->pickup_address,
                    'pickup_reference' => $assistanceRequest->pickup_reference,
                ],
                'history' => $history,
                'events' => $events,
            ],
        ], 200);
    }
}
