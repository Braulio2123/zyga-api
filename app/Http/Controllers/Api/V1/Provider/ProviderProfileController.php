<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\StatusType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderProfileController extends Controller
{
    protected function pendingProviderStatusId(): ?int
    {
        return StatusType::query()
            ->where('code', 'pending')
            ->whereHas('domain', function ($query) {
                $query->where('code', 'provider');
            })
            ->value('id');
    }

    public function show(Request $request): JsonResponse
    {
        $provider = Provider::query()
            ->with(['status.domain', 'services', 'schedules', 'documents'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Perfil de proveedor obtenido correctamente.',
            'data' => [
                ...$provider->toArray(),
                'can_operate' => (bool) ($provider->is_verified && optional($provider->status)->code === 'active'),
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $existingProvider = Provider::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingProvider) {
            return response()->json([
                'message' => 'El usuario autenticado ya tiene un proveedor registrado.',
                'data' => $existingProvider->load(['status', 'services', 'schedules', 'documents']),
            ], 422);
        }

        $pendingStatusId = $this->pendingProviderStatusId();

        if (!$pendingStatusId) {
            return response()->json([
                'message' => 'No existe un estatus inicial de proveedor configurado (pending).',
            ], 500);
        }

        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'provider_kind' => ['nullable', 'string', 'max:100'],
        ]);

        $provider = Provider::create([
            'user_id' => $request->user()->id,
            'display_name' => trim($data['display_name']),
            'provider_kind' => $data['provider_kind'] ?? null,
            'status_id' => $pendingStatusId,
            'is_verified' => false,
        ]);

        return response()->json([
            'message' => 'Proveedor registrado correctamente. Quedó en estado pendiente de validación administrativa.',
            'data' => $provider->load(['status.domain', 'services', 'schedules', 'documents']),
        ], 201);
    }

    public function update(Request $request): JsonResponse
    {
        $provider = Provider::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $data = $request->validate([
            'display_name' => ['sometimes', 'string', 'max:255'],
            'provider_kind' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        if (array_key_exists('display_name', $data)) {
            $provider->display_name = trim($data['display_name']);
        }

        if (array_key_exists('provider_kind', $data)) {
            $provider->provider_kind = $data['provider_kind'];
        }

        $provider->save();

        return response()->json([
            'message' => 'Perfil de proveedor actualizado correctamente.',
            'data' => $provider->load(['status.domain', 'services', 'schedules', 'documents']),
        ], 200);
    }

    public function destroy(Request $request): JsonResponse
    {
        $provider = Provider::query()
            ->withCount(['assistanceRequests as active_requests_count' => function ($query) {
                $query->whereIn('status', ['created', 'assigned', 'in_progress']);
            }])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        if ($provider->active_requests_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el perfil del proveedor mientras tenga solicitudes activas.',
            ], 422);
        }

        $provider->delete();

        return response()->json([
            'message' => 'Perfil de proveedor eliminado correctamente.',
        ], 200);
    }
}
