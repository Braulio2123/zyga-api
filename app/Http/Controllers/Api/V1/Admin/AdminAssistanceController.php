<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAssistanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AssistanceRequest::query()
            ->with(['user', 'provider', 'service', 'vehicle'])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->query('provider_id'));
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->query('service_id'));
        }

        if ($request->filled('public_id')) {
            $query->where('public_id', $request->query('public_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        $assistanceRequests = $query->get();

        return response()->json([
            'message' => 'Solicitudes de asistencia obtenidas correctamente.',
            'data' => $assistanceRequests,
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $assistanceRequest = AssistanceRequest::query()
            ->with(['user', 'provider', 'service', 'vehicle'])
            ->find($id);

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

    public function update(Request $request, int $id): JsonResponse
    {
        $assistanceRequest = AssistanceRequest::query()->find($id);

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'Solicitud de asistencia no encontrada.',
            ], 404);
        }

        $original = [
            'provider_id' => $assistanceRequest->provider_id,
            'status' => $assistanceRequest->status,
            'cancel_reason' => $assistanceRequest->cancel_reason,
        ];

        $data = $request->validate([
            'provider_id' => ['sometimes', 'nullable', 'integer', 'exists:providers,id'],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['created', 'assigned', 'in_progress', 'completed', 'cancelled']),
            ],
            'cancel_reason' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        if (empty($data)) {
            return response()->json([
                'message' => 'No se enviaron campos válidos para actualizar.',
            ], 422);
        }

        if (
            array_key_exists('status', $data)
            && $data['status'] === 'cancelled'
            && !array_key_exists('cancel_reason', $data)
            && empty($assistanceRequest->cancel_reason)
        ) {
            return response()->json([
                'message' => 'Debe indicar un motivo de cancelación al cancelar la solicitud.',
                'errors' => [
                    'cancel_reason' => [
                        'Debe indicar un motivo de cancelación al cancelar la solicitud.',
                    ],
                ],
            ], 422);
        }

        if (
            array_key_exists('status', $data)
            && $data['status'] !== 'cancelled'
            && array_key_exists('cancel_reason', $data)
            && !is_null($data['cancel_reason'])
        ) {
            return response()->json([
                'message' => 'cancel_reason solo puede enviarse cuando el status es cancelled.',
                'errors' => [
                    'cancel_reason' => [
                        'cancel_reason solo puede enviarse cuando el status es cancelled.',
                    ],
                ],
            ], 422);
        }

        if (array_key_exists('provider_id', $data)) {
            $assistanceRequest->provider_id = $data['provider_id'];
        }

        if (array_key_exists('status', $data)) {
            $assistanceRequest->status = $data['status'];
        }

        if (array_key_exists('cancel_reason', $data)) {
            $assistanceRequest->cancel_reason = $data['cancel_reason'];
        }

        $assistanceRequest->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.assistance_request.updated',
            'description' => json_encode([
                'assistance_request_id' => $assistanceRequest->id,
                'public_id' => $assistanceRequest->public_id,
                'before' => $original,
                'after' => [
                    'provider_id' => $assistanceRequest->provider_id,
                    'status' => $assistanceRequest->status,
                    'cancel_reason' => $assistanceRequest->cancel_reason,
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json([
            'message' => 'Solicitud de asistencia actualizada correctamente.',
            'data' => $assistanceRequest->load(['user', 'provider', 'service', 'vehicle']),
        ], 200);
    }
}