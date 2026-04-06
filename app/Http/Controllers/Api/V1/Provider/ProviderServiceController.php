<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $provider = Provider::query()
            ->with(['services' => function ($query) {
                $query->orderBy('id');
            }])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Servicios del proveedor obtenidos correctamente.',
            'data' => [
                'provider_id' => $provider->id,
                'display_name' => $provider->display_name,
                'services' => $provider->services,
            ],
        ], 200);
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
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'distinct', 'exists:services,id'],
        ]);

        $validActiveServiceIds = Service::query()
            ->whereIn('id', $data['service_ids'])
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        if (count($validActiveServiceIds) !== count($data['service_ids'])) {
            return response()->json([
                'message' => 'Uno o más servicios no existen o no están activos.',
            ], 422);
        }

        $provider->services()->sync($validActiveServiceIds);
        $provider->load(['services' => function ($query) {
            $query->orderBy('id');
        }]);

        return response()->json([
            'message' => 'Servicios del proveedor actualizados correctamente.',
            'data' => [
                'provider_id' => $provider->id,
                'display_name' => $provider->display_name,
                'services' => $provider->services,
            ],
        ], 200);
    }
}