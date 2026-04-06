<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Provider;
use App\Models\StatusType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminProviderController extends Controller
{
    protected function resolveProviderStatusId(?int $statusId): ?int
    {
        if (is_null($statusId)) {
            return null;
        }

        return StatusType::query()
            ->where('id', $statusId)
            ->whereHas('domain', function ($query) {
                $query->where('code', 'provider');
            })
            ->value('id');
    }

    public function index(Request $request): JsonResponse
    {
        $providers = Provider::query()
            ->with(['user', 'status', 'services', 'schedules', 'documents'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Proveedores obtenidos correctamente.',
            'data' => $providers,
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $provider = Provider::query()
            ->with(['user', 'status', 'services', 'schedules', 'documents', 'assistanceRequests'])
            ->find($id);

        if (!$provider) {
            return response()->json([
                'message' => 'Proveedor no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Proveedor obtenido correctamente.',
            'data' => $provider,
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $provider = Provider::query()->find($id);

        if (!$provider) {
            return response()->json([
                'message' => 'Proveedor no encontrado.',
            ], 404);
        }

        $original = $provider->only([
            'display_name',
            'provider_kind',
            'status_id',
            'is_verified',
        ]);

        $data = $request->validate([
            'display_name' => ['sometimes', 'string', 'max:255'],
            'provider_kind' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status_id' => ['sometimes', 'integer', Rule::exists('status_types', 'id')],
            'is_verified' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('status_id', $data)) {
            $statusId = $this->resolveProviderStatusId($data['status_id']);

            if (!$statusId) {
                return response()->json([
                    'message' => 'El status_id indicado no pertenece al dominio de proveedor.',
                    'errors' => [
                        'status_id' => [
                            'El status_id indicado no pertenece al dominio de proveedor.',
                        ],
                    ],
                ], 422);
            }

            $data['status_id'] = $statusId;
        }

        if (array_key_exists('display_name', $data)) {
            $provider->display_name = trim($data['display_name']);
        }

        if (array_key_exists('provider_kind', $data)) {
            $provider->provider_kind = $data['provider_kind'];
        }

        if (array_key_exists('status_id', $data)) {
            $provider->status_id = $data['status_id'];
        }

        if (array_key_exists('is_verified', $data)) {
            $provider->is_verified = $data['is_verified'];
        }

        $provider->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.provider.updated',
            'description' => json_encode([
                'provider_id' => $provider->id,
                'before' => $original,
                'after' => $provider->only([
                    'display_name',
                    'provider_kind',
                    'status_id',
                    'is_verified',
                ]),
            ], JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json([
            'message' => 'Proveedor actualizado correctamente.',
            'data' => $provider->load(['user', 'status', 'services', 'schedules', 'documents']),
        ], 200);
    }
}