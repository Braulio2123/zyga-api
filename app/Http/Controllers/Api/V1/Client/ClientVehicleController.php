<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientVehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'VehÃ­culos obtenidos correctamente.',
            'data' => $vehicles,
        ], 200);
    }

    public function store(Request $request)
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
        ]);

        return response()->json([
            'message' => 'VehÃ­culo registrado correctamente.',
            'data' => $vehicle,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $vehicle = Vehicle::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'message' => 'VehÃ­culo no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'VehÃ­culo obtenido correctamente.',
            'data' => $vehicle,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'message' => 'VehÃ­culo no encontrado.',
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

        return response()->json([
            'message' => 'VehÃ­culo actualizado correctamente.',
            'data' => $vehicle,
        ], 200);
    }

    public function destroy(Request $request, string $id)
    {
        $vehicle = Vehicle::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'message' => 'VehÃ­culo no encontrado.',
            ], 404);
        }

        $vehicle->delete();

        return response()->json([
            'message' => 'VehÃ­culo eliminado correctamente.',
        ], 200);
    }
}
