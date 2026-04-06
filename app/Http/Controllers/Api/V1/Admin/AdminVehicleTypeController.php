<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminVehicleTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VehicleType::query()->orderBy('id');

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $vehicleTypes = $query->get();

        return response()->json([
            'message' => 'Tipos de vehículo obtenidos correctamente.',
            'data' => $vehicleTypes,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:vehicle_types,code'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $vehicleType = VehicleType::create([
            'code' => strtolower(trim($data['code'])),
            'name' => trim($data['name']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Tipo de vehículo creado correctamente.',
            'data' => $vehicleType,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $vehicleType = VehicleType::query()->find($id);

        if (!$vehicleType) {
            return response()->json([
                'message' => 'Tipo de vehículo no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Tipo de vehículo obtenido correctamente.',
            'data' => $vehicleType,
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $vehicleType = VehicleType::query()->find($id);

        if (!$vehicleType) {
            return response()->json([
                'message' => 'Tipo de vehículo no encontrado.',
            ], 404);
        }

        $data = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('vehicle_types', 'code')->ignore($vehicleType->id),
            ],
            'name' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('code', $data)) {
            $vehicleType->code = strtolower(trim($data['code']));
        }

        if (array_key_exists('name', $data)) {
            $vehicleType->name = trim($data['name']);
        }

        if (array_key_exists('is_active', $data)) {
            $vehicleType->is_active = $data['is_active'];
        }

        $vehicleType->save();

        return response()->json([
            'message' => 'Tipo de vehículo actualizado correctamente.',
            'data' => $vehicleType,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $vehicleType = VehicleType::query()->find($id);

        if (!$vehicleType) {
            return response()->json([
                'message' => 'Tipo de vehículo no encontrado.',
            ], 404);
        }

        if ($vehicleType->vehicles()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el tipo de vehículo porque tiene vehículos asociados.',
            ], 422);
        }

        $vehicleType->delete();

        return response()->json([
            'message' => 'Tipo de vehículo eliminado correctamente.',
        ], 200);
    }
}