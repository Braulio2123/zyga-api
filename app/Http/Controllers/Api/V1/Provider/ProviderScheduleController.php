<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderScheduleController extends Controller
{
    protected function resolveProvider(Request $request): ?Provider
    {
        return Provider::query()
            ->where('user_id', $request->user()->id)
            ->first();
    }

    public function index(Request $request): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $schedules = ProviderSchedule::query()
            ->where('provider_id', $provider->id)
            ->orderBy('day_of_week')
            ->get();

        return response()->json([
            'message' => 'Horarios del proveedor obtenidos correctamente.',
            'data' => [
                'provider_id' => $provider->id,
                'display_name' => $provider->display_name,
                'schedules' => $schedules,
            ],
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'min:1', 'max:7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $exists = ProviderSchedule::query()
            ->where('provider_id', $provider->id)
            ->where('day_of_week', $data['day_of_week'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un horario registrado para ese día.',
            ], 422);
        }

        $schedule = ProviderSchedule::create([
            'provider_id' => $provider->id,
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'timezone' => $data['timezone'] ?? 'America/Mexico_City',
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Horario del proveedor creado correctamente.',
            'data' => $schedule,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $schedule = ProviderSchedule::query()
            ->where('provider_id', $provider->id)
            ->where('id', $id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'message' => 'Horario no encontrado para el proveedor autenticado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Horario obtenido correctamente.',
            'data' => $schedule,
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $schedule = ProviderSchedule::query()
            ->where('provider_id', $provider->id)
            ->where('id', $id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'message' => 'Horario no encontrado para el proveedor autenticado.',
            ], 404);
        }

        $data = $request->validate([
            'day_of_week' => ['sometimes', 'integer', 'min:1', 'max:7'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i'],
            'timezone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $newDay = $data['day_of_week'] ?? $schedule->day_of_week;
        $startTime = $data['start_time'] ?? $schedule->start_time;
        $endTime = $data['end_time'] ?? $schedule->end_time;

        if (strtotime($endTime) <= strtotime($startTime)) {
            return response()->json([
                'message' => 'La hora de fin debe ser mayor que la hora de inicio.',
            ], 422);
        }

        $duplicateDay = ProviderSchedule::query()
            ->where('provider_id', $provider->id)
            ->where('day_of_week', $newDay)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($duplicateDay) {
            return response()->json([
                'message' => 'Ya existe otro horario registrado para ese día.',
            ], 422);
        }

        $schedule->fill($data);
        $schedule->save();

        return response()->json([
            'message' => 'Horario del proveedor actualizado correctamente.',
            'data' => $schedule,
        ], 200);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $schedule = ProviderSchedule::query()
            ->where('provider_id', $provider->id)
            ->where('id', $id)
            ->first();

        if (!$schedule) {
            return response()->json([
                'message' => 'Horario no encontrado para el proveedor autenticado.',
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'message' => 'Horario del proveedor eliminado correctamente.',
        ], 200);
    }
}