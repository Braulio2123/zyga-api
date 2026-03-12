<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientAssistanceRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = AssistanceRequest::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Solicitudes obtenidas correctamente.',
            'data' => $requests,
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'vehicle_id' => [
                'nullable',
                'integer',
                Rule::exists('vehicles', 'id')->where(function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                }),
            ],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'pickup_address' => ['nullable', 'string', 'max:255'],
        ]);

        $assistanceRequest = AssistanceRequest::create([
            'public_id' => (string) Str::upper(Str::random(26)),
            'user_id' => $request->user()->id,
            'provider_id' => null,
            'service_id' => $data['service_id'],
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'pickup_address' => $data['pickup_address'] ?? null,
            'status' => 'created',
        ]);

        return response()->json([
            'message' => 'Solicitud de asistencia registrada correctamente.',
            'data' => $assistanceRequest,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud no encontrada.',
            ], 404);
        }

        return response()->json([
            'message' => 'Solicitud obtenida correctamente.',
            'data' => $assistanceRequest,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        return response()->json([
            'message' => 'MÃ©todo no habilitado para este recurso.',
        ], 405);
    }

    public function destroy(Request $request, string $id)
    {
        return response()->json([
            'message' => 'MÃ©todo no habilitado para este recurso.',
        ], 405);
    }

    public function cancel(Request $request, string $id)
    {
        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud no encontrada.',
            ], 404);
        }

        if (in_array($assistanceRequest->status, ['completed', 'cancelled'], true)) {
            return response()->json([
                'message' => 'La solicitud ya no puede cancelarse.',
            ], 422);
        }

        $assistanceRequest->status = 'cancelled';
        $assistanceRequest->save();

        return response()->json([
            'message' => 'Solicitud cancelada correctamente.',
            'data' => $assistanceRequest,
        ], 200);
    }

    public function changeStatus(Request $request, string $id)
    {
        $assistanceRequest = AssistanceRequest::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud no encontrada.',
            ], 404);
        }

        $data = $request->validate([
            'status' => ['required', Rule::in([
                'created',
                'assigned',
                'in_progress',
                'completed',
                'cancelled',
            ])],
        ]);

        $assistanceRequest->status = $data['status'];
        $assistanceRequest->save();

        return response()->json([
            'message' => 'Estado actualizado correctamente.',
            'data' => $assistanceRequest,
        ], 200);
    }
}
