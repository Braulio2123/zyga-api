<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\RequestEvent;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientServiceRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $serviceRequests = ServiceRequest::query()
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['assistanceRequest.service', 'assistanceRequest.vehicle', 'service', 'provider'])
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Solicitudes de servicio obtenidas correctamente.',
            'data' => $serviceRequests,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'assistance_request_id' => ['required', 'integer', 'exists:assistance_requests,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'provider_id' => ['nullable', 'integer', 'exists:providers,id'],
        ]);

        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $data['assistance_request_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'La asistencia indicada no pertenece al usuario autenticado.',
            ], 403);
        }

        $serviceRequest = ServiceRequest::create([
            'assistance_request_id' => $data['assistance_request_id'],
            'service_id' => $data['service_id'],
            'provider_id' => $data['provider_id'] ?? null,
            'status' => 'created',
        ]);

        RequestEvent::create([
            'request_id' => $assistanceRequest->id,
            'event_type' => 'service_request_created',
            'event_data' => [
                'message' => 'Se creó una solicitud de servicio vinculada a la asistencia.',
                'service_request_id' => $serviceRequest->id,
                'service_id' => $serviceRequest->service_id,
                'provider_id' => $serviceRequest->provider_id,
                'status' => $serviceRequest->status,
            ],
        ]);

        $serviceRequest->load(['assistanceRequest.service', 'assistanceRequest.vehicle', 'service', 'provider']);

        return response()->json([
            'message' => 'Solicitud de servicio registrada correctamente.',
            'data' => $serviceRequest,
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::query()
            ->where('id', $id)
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['assistanceRequest.service', 'assistanceRequest.vehicle', 'service', 'provider'])
            ->first();

        if (!$serviceRequest) {
            return response()->json([
                'message' => 'Solicitud de servicio no encontrada.',
            ], 404);
        }

        return response()->json([
            'message' => 'Solicitud de servicio obtenida correctamente.',
            'data' => $serviceRequest,
        ], 200);
    }

    public function quote(Request $request, string $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::query()
            ->where('id', $id)
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->first();

        if (!$serviceRequest) {
            return response()->json([
                'message' => 'Solicitud de servicio no encontrada.',
            ], 404);
        }

        $data = $request->validate([
            'quoted_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $serviceRequest->status = 'quoted';
        $serviceRequest->save();

        RequestEvent::create([
            'request_id' => $serviceRequest->assistance_request_id,
            'event_type' => 'service_request_quoted',
            'event_data' => [
                'message' => 'Se registró una cotización para la solicitud de servicio.',
                'service_request_id' => $serviceRequest->id,
                'quoted_amount' => $data['quoted_amount'],
                'notes' => $data['notes'] ?? null,
                'status' => $serviceRequest->status,
            ],
        ]);

        return response()->json([
            'message' => 'Cotización registrada correctamente.',
            'data' => [
                'id' => $serviceRequest->id,
                'status' => $serviceRequest->status,
                'quoted_amount' => $data['quoted_amount'],
                'notes' => $data['notes'] ?? null,
            ],
        ], 200);
    }

    public function confirm(Request $request, string $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::query()
            ->where('id', $id)
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->first();

        if (!$serviceRequest) {
            return response()->json([
                'message' => 'Solicitud de servicio no encontrada.',
            ], 404);
        }

        if (!in_array($serviceRequest->status, ['created', 'quoted'], true)) {
            return response()->json([
                'message' => 'La solicitud no puede confirmarse en su estado actual.',
                'data' => [
                    'current_status' => $serviceRequest->status,
                ],
            ], 422);
        }

        $serviceRequest->status = 'confirmed';
        $serviceRequest->save();

        RequestEvent::create([
            'request_id' => $serviceRequest->assistance_request_id,
            'event_type' => 'service_request_confirmed',
            'event_data' => [
                'message' => 'El cliente confirmó la solicitud de servicio.',
                'service_request_id' => $serviceRequest->id,
                'status' => $serviceRequest->status,
            ],
        ]);

        return response()->json([
            'message' => 'Solicitud de servicio confirmada correctamente.',
            'data' => [
                'id' => $serviceRequest->id,
                'status' => $serviceRequest->status,
            ],
        ], 200);
    }
}