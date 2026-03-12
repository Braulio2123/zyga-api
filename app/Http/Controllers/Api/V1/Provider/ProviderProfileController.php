<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProviderProfileController extends Controller
{
    public function index(Request $request)
    {
        $providers = Provider::query()
            ->with(['services', 'schedules'])
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Proveedores obtenidos correctamente.',
            'data' => $providers,
        ], 200);
    }

    public function store(Request $request)
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
            'status_id' => ['required', 'integer', 'exists:status_types,id'],
        ]);

        $provider = Provider::create([
            'user_id' => $request->user()->id,
            'display_name' => trim($data['display_name']),
            'provider_kind' => $data['provider_kind'] ?? null,
            'status_id' => $data['status_id'],
            'is_verified' => false,
        ]);

        return response()->json([
            'message' => 'Proveedor registrado correctamente.',
            'data' => $provider,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $provider = Provider::query()
            ->with(['services', 'schedules'])
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

    public function update(Request $request, string $id)
    {
        $provider = Provider::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'Proveedor no encontrado o no pertenece al usuario autenticado.',
            ], 404);
        }

        $data = $request->validate([
            'display_name' => ['sometimes', 'string', 'max:255'],
            'provider_kind' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status_id' => ['sometimes', 'integer', 'exists:status_types,id'],
            'is_verified' => ['sometimes', 'boolean'],
        ]);

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

        return response()->json([
            'message' => 'Proveedor actualizado correctamente.',
            'data' => $provider,
        ], 200);
    }

    public function destroy(Request $request, string $id)
    {
        $provider = Provider::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'Proveedor no encontrado o no pertenece al usuario autenticado.',
            ], 404);
        }

        $provider->delete();

        return response()->json([
            'message' => 'Proveedor eliminado correctamente.',
        ], 200);
    }

    public function updateServices(Request $request, string $id)
    {
        $provider = Provider::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'Proveedor no encontrado o no pertenece al usuario autenticado.',
            ], 404);
        }

        $data = $request->validate([
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
        ]);

        $provider->services()->sync($data['service_ids']);

        return response()->json([
            'message' => 'Servicios del proveedor actualizados correctamente.',
            'data' => $provider->load('services'),
        ], 200);
    }

    public function updateSchedule(Request $request, string $id)
    {
        $provider = Provider::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$provider) {
            return response()->json([
                'message' => 'Proveedor no encontrado o no pertenece al usuario autenticado.',
            ], 404);
        }

        $data = $request->validate([
            'schedule' => ['required', 'array', 'min:1'],
            'schedule.*.day_of_week' => ['required', 'integer', 'min:1', 'max:7'],
            'schedule.*.start_time' => ['required', 'date_format:H:i'],
            'schedule.*.end_time' => ['required', 'date_format:H:i'],
            'schedule.*.timezone' => ['nullable', 'string', 'max:50'],
            'schedule.*.is_active' => ['sometimes', 'boolean'],
        ]);

        $provider->schedules()->delete();

        foreach ($data['schedule'] as $row) {
            $provider->schedules()->create([
                'day_of_week' => $row['day_of_week'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'timezone' => $row['timezone'] ?? 'America/Mexico_City',
                'is_active' => $row['is_active'] ?? true,
            ]);
        }

        return response()->json([
            'message' => 'Horario del proveedor actualizado correctamente.',
            'data' => $provider->load('schedules'),
        ], 200);
    }
}
