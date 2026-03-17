<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\RequestEvent;
use App\Models\RequestHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientAssistanceRequestController extends Controller
{
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
        ]);

        $vehicle = $request->user()->vehicles()
            ->where('id', $data['vehicle_id'])
            ->first();

        if (!$vehicle) {
            return response()->json([
                'message' => 'El vehículo seleccionado no pertenece al usuario autenticado.',
            ], 403);
        }

        $assistanceRequest = AssistanceRequest::create([
            'public_id' => 'REQ-' . strtoupper(Str::random(10)),
            'user_id' => $request->user()->id,
            'provider_id' => null,
            'service_id' => $data['service_id'],
            'vehicle_id' => $data['vehicle_id'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'pickup_address' => trim($data['pickup_address']),
            'status' => 'created',
        ]);

        RequestHistory::create([
            'request_id' => $assistanceRequest->id,
            'status' => 'created',
        ]);

        RequestEvent::create([
            'request_id' => $assistanceRequest->id,
            'event_type' => 'created',
            'event_data' => [
                'message' => 'Solicitud creada por el cliente.',
                'public_id' => $assistanceRequest->public_id,
                'service_id' => $assistanceRequest->service_id,
                'vehicle_id' => $assistanceRequest->vehicle_id,
                'lat' => $assistanceRequest->lat,
                'lng' => $assistanceRequest->lng,
                'pickup_address' => $assistanceRequest->pickup_address,
                'status' => $assistanceRequest->status,
            ],
        ]);

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

        if (!in_array($assistanceRequest->status, ['created', 'assigned'], true)) {
            return response()->json([
                'message' => 'La solicitud no puede cancelarse en su estado actual.',
                'data' => [
                    'current_status' => $assistanceRequest->status,
                ],
            ], 422);
        }

        $assistanceRequest->status = 'cancelled';
        $assistanceRequest->save();

        RequestHistory::create([
            'request_id' => $assistanceRequest->id,
            'status' => 'cancelled',
        ]);

        RequestEvent::create([
            'request_id' => $assistanceRequest->id,
            'event_type' => 'cancelled',
            'event_data' => [
                'message' => 'Solicitud cancelada por el cliente.',
                'status' => 'cancelled',
            ],
        ]);

        return response()->json([
            'message' => 'Solicitud de asistencia cancelada correctamente.',
            'data' => [
                'id' => $assistanceRequest->id,
                'public_id' => $assistanceRequest->public_id,
                'status' => $assistanceRequest->status,
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

        $history = RequestHistory::query()
            ->where('request_id', $assistanceRequest->id)
            ->orderBy('id')
            ->get();

        $events = RequestEvent::query()
            ->where('request_id', $assistanceRequest->id)
            ->orderBy('id')
            ->get();

        return response()->json([
            'message' => 'Línea de tiempo obtenida correctamente.',
            'data' => [
                'request' => [
                    'id' => $assistanceRequest->id,
                    'public_id' => $assistanceRequest->public_id,
                    'status' => $assistanceRequest->status,
                ],
                'history' => $history,
                'events' => $events,
            ],
        ], 200);
    }
}