<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\StatusType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProviderProfileController extends Controller
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

    public function show(Request $request): JsonResponse
    {
        $provider = Provider::query()
            ->with(['status', 'services', 'schedules'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Perfil de proveedor obtenido correctamente.',
            'data' => $provider,
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
                'data' => $existingProvider,
            ], 422);
        }

        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'provider_kind' => ['nullable', 'string', 'max:100'],
            'status_id' => ['required', 'integer', Rule::exists('status_types', 'id')],
        ]);

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

        $provider = Provider::create([
            'user_id' => $request->user()->id,
            'display_name' => trim($data['display_name']),
            'provider_kind' => $data['provider_kind'] ?? null,
            'status_id' => $statusId,
            'is_verified' => false,
        ]);

        return response()->json([
            'message' => 'Proveedor registrado correctamente.',
            'data' => $provider->load(['status', 'services', 'schedules']),
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
            'data' => $provider->load(['status', 'services', 'schedules']),
        ], 200);
    }

    public function destroy(Request $request): JsonResponse
    {
        $provider = Provider::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $provider->delete();

        return response()->json([
            'message' => 'Perfil de proveedor eliminado correctamente.',
        ], 200);
    }
}