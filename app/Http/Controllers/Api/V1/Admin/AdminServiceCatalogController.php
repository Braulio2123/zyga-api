<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminServiceCatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $services = Service::query()
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Servicios obtenidos correctamente.',
            'data' => $services,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:services,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service = Service::create([
            'code' => trim($data['code']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Servicio creado correctamente.',
            'data' => $service,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $service = Service::query()->find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Servicio no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Servicio obtenido correctamente.',
            'data' => $service,
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $service = Service::query()->find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Servicio no encontrado.',
            ], 404);
        }

        $data = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('services', 'code')->ignore($service->id),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service->fill($data);
        $service->save();

        return response()->json([
            'message' => 'Servicio actualizado correctamente.',
            'data' => $service,
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $service = Service::query()->find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Servicio no encontrado.',
            ], 404);
        }

        $service->delete();

        return response()->json([
            'message' => 'Servicio eliminado correctamente.',
        ], 200);
    }
}