<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderDocumentController extends Controller
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

        $documents = ProviderDocument::query()
            ->where('provider_id', $provider->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Documentos del proveedor obtenidos correctamente.',
            'data' => [
                'provider_id' => $provider->id,
                'display_name' => $provider->display_name,
                'documents' => $documents,
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
            'document_type' => ['required', 'string', 'max:255'],
            'document_url' => ['required', 'string', 'max:2048'],
        ]);

        $document = ProviderDocument::create([
            'provider_id' => $provider->id,
            'document_type' => trim($data['document_type']),
            'document_url' => trim($data['document_url']),
        ]);

        return response()->json([
            'message' => 'Documento del proveedor creado correctamente.',
            'data' => $document,
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

        $document = ProviderDocument::query()
            ->where('provider_id', $provider->id)
            ->where('id', $id)
            ->first();

        if (!$document) {
            return response()->json([
                'message' => 'Documento no encontrado para el proveedor autenticado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Documento obtenido correctamente.',
            'data' => $document,
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

        $document = ProviderDocument::query()
            ->where('provider_id', $provider->id)
            ->where('id', $id)
            ->first();

        if (!$document) {
            return response()->json([
                'message' => 'Documento no encontrado para el proveedor autenticado.',
            ], 404);
        }

        $document->delete();

        return response()->json([
            'message' => 'Documento del proveedor eliminado correctamente.',
        ], 200);
    }
}