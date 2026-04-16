<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Provider;
use App\Services\AssistanceRequestLifecycleService;
use App\Support\AssistanceRequestFlow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminAssistanceController extends Controller
{
    public function __construct(
        protected AssistanceRequestLifecycleService $lifecycle,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = AssistanceRequest::query()
            ->with([
                'user',
                'provider',
                'provider.user',
                'service',
                'vehicle',
                'latestProviderLocation',
            ])
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
            ->with([
                'user',
                'provider',
                'provider.user',
                'service',
                'vehicle',
                'history',
                'events',
                'payment',
                'latestProviderLocation',
            ])
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
                Rule::in(AssistanceRequestFlow::statuses()),
            ],
            'cancel_reason' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        if (empty($data)) {
            return response()->json([
                'message' => 'No se enviaron campos válidos para actualizar.',
            ], 422);
        }

        $newStatus = $data['status'] ?? $assistanceRequest->status;
        $newProviderId = array_key_exists('provider_id', $data) ? $data['provider_id'] : $assistanceRequest->provider_id;

        if (!AssistanceRequestFlow::canAdminTransition($assistanceRequest->status, $newStatus)) {
            return response()->json([
                'message' => "No se permite cambiar el estado de {$assistanceRequest->status} a {$newStatus}.",
            ], 422);
        }

        if ($newStatus === AssistanceRequestFlow::ASSIGNED && is_null($newProviderId)) {
            return response()->json([
                'message' => 'No se puede asignar el estatus assigned sin provider_id.',
                'errors' => [
                    'provider_id' => ['No se puede asignar el estatus assigned sin provider_id.'],
                ],
            ], 422);
        }

        if ($newStatus === AssistanceRequestFlow::CANCELLED && empty($data['cancel_reason']) && empty($assistanceRequest->cancel_reason)) {
            return response()->json([
                'message' => 'Debe indicar un motivo de cancelación al cancelar la solicitud.',
                'errors' => [
                    'cancel_reason' => ['Debe indicar un motivo de cancelación al cancelar la solicitud.'],
                ],
            ], 422);
        }

        if (!is_null($newProviderId)) {
            $provider = Provider::query()->with('services')->find($newProviderId);

            if (!$provider) {
                return response()->json([
                    'message' => 'El proveedor indicado no existe.',
                ], 422);
            }

            $providerCanDoService = $provider->services->contains('id', $assistanceRequest->service_id);

            if (!$providerCanDoService) {
                return response()->json([
                    'message' => 'El proveedor indicado no ofrece el servicio solicitado.',
                ], 422);
            }
        }

        DB::transaction(function () use ($request, $assistanceRequest, $data, $newProviderId, $newStatus, $original) {
            if (array_key_exists('provider_id', $data)) {
                $assistanceRequest->provider_id = $newProviderId;
            }

            if (array_key_exists('status', $data)) {
                $assistanceRequest->status = $newStatus;
            }

            if (array_key_exists('cancel_reason', $data)) {
                $assistanceRequest->cancel_reason = $data['cancel_reason'];
            }

            $assistanceRequest->save();

            if ($original['status'] !== $assistanceRequest->status) {
                $this->lifecycle->createTimelineEntry(
                    $assistanceRequest,
                    $assistanceRequest->status,
                    'admin_status_updated',
                    [
                        'from' => $original['status'],
                        'to' => $assistanceRequest->status,
                        'admin_user_id' => $request->user()->id,
                        'cancel_reason' => $assistanceRequest->cancel_reason,
                    ]
                );
            } elseif ($original['provider_id'] !== $assistanceRequest->provider_id) {
                $this->lifecycle->createTimelineEntry(
                    $assistanceRequest,
                    $assistanceRequest->status,
                    'admin_provider_reassigned',
                    [
                        'from_provider_id' => $original['provider_id'],
                        'to_provider_id' => $assistanceRequest->provider_id,
                        'admin_user_id' => $request->user()->id,
                    ]
                );
            }

            $this->lifecycle->notifyUser(
                $assistanceRequest->user_id,
                'assistance_request',
                'Tu solicitud fue actualizada por administración.'
            );

            if (!is_null($assistanceRequest->provider?->user_id)) {
                $this->lifecycle->notifyUser(
                    $assistanceRequest->provider->user_id,
                    'assistance_request',
                    'Administración actualizó una solicitud relacionada con tu operación.'
                );
            }

            $this->lifecycle->audit(
                $request->user()->id,
                'admin.assistance_request.updated',
                [
                    'assistance_request_id' => $assistanceRequest->id,
                    'public_id' => $assistanceRequest->public_id,
                    'before' => $original,
                    'after' => [
                        'provider_id' => $assistanceRequest->provider_id,
                        'status' => $assistanceRequest->status,
                        'cancel_reason' => $assistanceRequest->cancel_reason,
                    ],
                ]
            );
        });

        $assistanceRequest->load([
            'user',
            'provider',
            'provider.user',
            'service',
            'vehicle',
            'history',
            'events',
            'payment',
            'latestProviderLocation',
        ]);

        return response()->json([
            'message' => 'Solicitud de asistencia actualizada correctamente.',
            'data' => $assistanceRequest,
        ], 200);
    }
}
