<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatusDomain;
use App\Models\StatusType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminStatusController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StatusType::query()
            ->with('domain')
            ->orderBy('domain_id')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($request->filled('domain_id')) {
            $query->where('domain_id', $request->query('domain_id'));
        }

        if ($request->filled('domain_code')) {
            $domain = StatusDomain::query()
                ->where('code', $request->query('domain_code'))
                ->first();

            if (!$domain) {
                return response()->json([
                    'message' => 'Dominio de estatus no encontrado.',
                ], 404);
            }

            $query->where('domain_id', $domain->id);
        }

        if ($request->has('is_active')) {
            $query->where(
                'is_active',
                filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN)
            );
        }

        if ($request->has('is_terminal')) {
            $query->where(
                'is_terminal',
                filter_var($request->query('is_terminal'), FILTER_VALIDATE_BOOLEAN)
            );
        }

        $statuses = $query->get();

        return response()->json([
            'message' => 'Estatus obtenidos correctamente.',
            'data' => $statuses,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'domain_id' => ['required', 'integer', 'exists:status_domains,id'],
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
            'is_terminal' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $normalizedCode = strtolower(trim($data['code']));

        $exists = StatusType::query()
            ->where('domain_id', $data['domain_id'])
            ->where('code', $normalizedCode)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un estatus con ese código en el dominio indicado.',
                'errors' => [
                    'code' => [
                        'Ya existe un estatus con ese código en el dominio indicado.',
                    ],
                ],
            ], 422);
        }

        $status = StatusType::create([
            'domain_id' => $data['domain_id'],
            'code' => $normalizedCode,
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_terminal' => $data['is_terminal'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Estatus creado correctamente.',
            'data' => $status->load('domain'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $status = StatusType::query()
            ->with('domain')
            ->find($id);

        if (!$status) {
            return response()->json([
                'message' => 'Estatus no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Estatus obtenido correctamente.',
            'data' => $status,
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $status = StatusType::query()->find($id);

        if (!$status) {
            return response()->json([
                'message' => 'Estatus no encontrado.',
            ], 404);
        }

        $data = $request->validate([
            'domain_id' => ['sometimes', 'integer', 'exists:status_domains,id'],
            'code' => ['sometimes', 'string', 'max:50'],
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
            'is_terminal' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $targetDomainId = $data['domain_id'] ?? $status->domain_id;
        $targetCode = array_key_exists('code', $data)
            ? strtolower(trim($data['code']))
            : $status->code;

        $exists = StatusType::query()
            ->where('domain_id', $targetDomainId)
            ->where('code', $targetCode)
            ->where('id', '!=', $status->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un estatus con ese código en el dominio indicado.',
                'errors' => [
                    'code' => [
                        'Ya existe un estatus con ese código en el dominio indicado.',
                    ],
                ],
            ], 422);
        }

        if (array_key_exists('domain_id', $data)) {
            $status->domain_id = $data['domain_id'];
        }

        if (array_key_exists('code', $data)) {
            $status->code = strtolower(trim($data['code']));
        }

        if (array_key_exists('name', $data)) {
            $status->name = trim($data['name']);
        }

        if (array_key_exists('description', $data)) {
            $status->description = $data['description'];
        }

        if (array_key_exists('sort_order', $data)) {
            $status->sort_order = $data['sort_order'];
        }

        if (array_key_exists('is_terminal', $data)) {
            $status->is_terminal = $data['is_terminal'];
        }

        if (array_key_exists('is_active', $data)) {
            $status->is_active = $data['is_active'];
        }

        $status->save();

        return response()->json([
            'message' => 'Estatus actualizado correctamente.',
            'data' => $status->load('domain'),
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $status = StatusType::query()
            ->withCount('providers')
            ->find($id);

        if (!$status) {
            return response()->json([
                'message' => 'Estatus no encontrado.',
            ], 404);
        }

        if ($status->providers_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el estatus porque tiene proveedores asociados.',
            ], 422);
        }

        $status->delete();

        return response()->json([
            'message' => 'Estatus eliminado correctamente.',
        ], 200);
    }
}