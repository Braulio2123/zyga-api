<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Support\AssistanceRequestFlow;
use Illuminate\Validation\Rule;

class ClientVehicleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vehicles = Vehicle::query()
            ->with('vehicleType')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Vehículos obtenidos correctamente.',
            'data' => $vehicles,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'plate' => ['required', 'string', 'max:16', 'unique:vehicles,plate'],
            'brand' => ['required', 'string', 'max:60'],
            'model' => ['required', 'string', 'max:60'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $request->user()->id,
            'vehicle_type_id' => $data['vehicle_type_id'],
            'plate' => strtoupper(trim($data['plate'])),
            'brand' => trim($data['brand']),
            'model' => trim($data['model']),
            'year' => $data['year'] ?? null,
        ])->load('vehicleType');

        return response()->json([
            'message' => 'Vehículo registrado correctamente.',
            'data' => $vehicle,
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $vehicle = Vehicle::query()
            ->with('vehicleType')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'message' => 'Vehículo no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Vehículo obtenido correctamente.',
            'data' => $vehicle,
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $vehicle = Vehicle::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'message' => 'Vehículo no encontrado.',
            ], 404);
        }

        $data = $request->validate([
            'vehicle_type_id' => ['sometimes', 'integer', 'exists:vehicle_types,id'],
            'plate' => [
                'sometimes',
                'string',
                'max:16',
                Rule::unique('vehicles', 'plate')->ignore($vehicle->id),
            ],
            'brand' => ['sometimes', 'string', 'max:60'],
            'model' => ['sometimes', 'string', 'max:60'],
            'year' => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:2100'],
        ]);

        if (array_key_exists('vehicle_type_id', $data)) {
            $vehicle->vehicle_type_id = $data['vehicle_type_id'];
        }

        if (array_key_exists('plate', $data)) {
            $vehicle->plate = strtoupper(trim($data['plate']));
        }

        if (array_key_exists('brand', $data)) {
            $vehicle->brand = trim($data['brand']);
        }

        if (array_key_exists('model', $data)) {
            $vehicle->model = trim($data['model']);
        }

        if (array_key_exists('year', $data)) {
            $vehicle->year = $data['year'];
        }

        $vehicle->save();
        $vehicle->load('vehicleType');

        return response()->json([
            'message' => 'Vehículo actualizado correctamente.',
            'data' => $vehicle,
        ], 200);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $vehicle = Vehicle::query()
            ->withCount(['assistanceRequests as active_requests_count' => function ($query) {
                $query->whereIn('status', AssistanceRequestFlow::activeStatuses());
            }])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'message' => 'Vehículo no encontrado.',
            ], 404);
        }

        if ($vehicle->active_requests_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar un vehículo con solicitudes activas asociadas.',
            ], 422);
        }

        $vehicle->delete();

        return response()->json([
            'message' => 'Vehículo eliminado correctamente.',
        ], 200);
    }
}