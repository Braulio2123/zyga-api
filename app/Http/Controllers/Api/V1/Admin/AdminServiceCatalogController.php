<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminServiceCatalogController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Servicios obtenidos correctamente.',
            'data' => $services,
        ], 200);
    }

    public function store(Request $request)
    {
        return response()->json([
            'message' => 'MÃ©todo no habilitado para este recurso.',
        ], 405);
    }

    public function show(string $id)
    {
        $service = Service::query()
            ->where('is_active', true)
            ->find($id);

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

    public function update(Request $request, string $id)
    {
        return response()->json([
            'message' => 'MÃ©todo no habilitado para este recurso.',
        ], 405);
    }

    public function destroy(string $id)
    {
        return response()->json([
            'message' => 'MÃ©todo no habilitado para este recurso.',
        ], 405);
    }
}
