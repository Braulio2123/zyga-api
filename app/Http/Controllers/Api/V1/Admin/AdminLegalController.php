<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsentType;
use App\Models\LegalDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminLegalController extends Controller
{
    public function consentTypes(Request $request): JsonResponse
    {
        $query = ConsentType::query()->orderBy('id');

        if ($request->has('is_active')) {
            $query->where(
                'is_active',
                filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN)
            );
        }

        $consentTypes = $query->get();

        return response()->json([
            'message' => 'Tipos de consentimiento obtenidos correctamente.',
            'data' => $consentTypes,
        ], 200);
    }

    public function storeConsentType(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:consent_types,code'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $consentType = ConsentType::create([
            'code' => strtolower(trim($data['code'])),
            'name' => trim($data['name']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Tipo de consentimiento creado correctamente.',
            'data' => $consentType,
        ], 201);
    }

    public function showConsentType(int $id): JsonResponse
    {
        $consentType = ConsentType::query()->find($id);

        if (!$consentType) {
            return response()->json([
                'message' => 'Tipo de consentimiento no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Tipo de consentimiento obtenido correctamente.',
            'data' => $consentType,
        ], 200);
    }

    public function updateConsentType(Request $request, int $id): JsonResponse
    {
        $consentType = ConsentType::query()->find($id);

        if (!$consentType) {
            return response()->json([
                'message' => 'Tipo de consentimiento no encontrado.',
            ], 404);
        }

        $data = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('consent_types', 'code')->ignore($consentType->id),
            ],
            'name' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('code', $data)) {
            $consentType->code = strtolower(trim($data['code']));
        }

        if (array_key_exists('name', $data)) {
            $consentType->name = trim($data['name']);
        }

        if (array_key_exists('is_active', $data)) {
            $consentType->is_active = $data['is_active'];
        }

        $consentType->save();

        return response()->json([
            'message' => 'Tipo de consentimiento actualizado correctamente.',
            'data' => $consentType,
        ], 200);
    }

    public function deleteConsentType(int $id): JsonResponse
    {
        $consentType = ConsentType::query()
            ->withCount('legalDocuments')
            ->find($id);

        if (!$consentType) {
            return response()->json([
                'message' => 'Tipo de consentimiento no encontrado.',
            ], 404);
        }

        if ($consentType->legal_documents_count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el tipo de consentimiento porque tiene documentos legales asociados.',
            ], 422);
        }

        $consentType->delete();

        return response()->json([
            'message' => 'Tipo de consentimiento eliminado correctamente.',
        ], 200);
    }

    public function documents(Request $request): JsonResponse
    {
        $query = LegalDocument::query()
            ->with('consentType')
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('consent_type_id')) {
            $query->where('consent_type_id', $request->query('consent_type_id'));
        }

        if ($request->filled('version')) {
            $query->where('version', $request->query('version'));
        }

        if ($request->has('is_active')) {
            $query->where(
                'is_active',
                filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN)
            );
        }

        $documents = $query->get();

        return response()->json([
            'message' => 'Documentos legales obtenidos correctamente.',
            'data' => $documents,
        ], 200);
    }

    public function storeDocument(Request $request): JsonResponse
    {
        $data = $request->validate([
            'consent_type_id' => ['required', 'integer', 'exists:consent_types,id'],
            'version' => ['required', 'string', 'max:50'],
            'published_at' => ['required', 'date'],
            'content_hash' => ['required', 'string', 'size:64'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $exists = LegalDocument::query()
            ->where('consent_type_id', $data['consent_type_id'])
            ->where('version', trim($data['version']))
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un documento legal con esa versión para el tipo de consentimiento indicado.',
                'errors' => [
                    'version' => [
                        'Ya existe un documento legal con esa versión para el tipo de consentimiento indicado.',
                    ],
                ],
            ], 422);
        }

        $document = LegalDocument::create([
            'consent_type_id' => $data['consent_type_id'],
            'version' => trim($data['version']),
            'published_at' => $data['published_at'],
            'content_hash' => strtolower(trim($data['content_hash'])),
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Documento legal creado correctamente.',
            'data' => $document->load('consentType'),
        ], 201);
    }

    public function showDocument(int $id): JsonResponse
    {
        $document = LegalDocument::query()
            ->with('consentType')
            ->find($id);

        if (!$document) {
            return response()->json([
                'message' => 'Documento legal no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Documento legal obtenido correctamente.',
            'data' => $document,
        ], 200);
    }

    public function updateDocument(Request $request, int $id): JsonResponse
    {
        $document = LegalDocument::query()->find($id);

        if (!$document) {
            return response()->json([
                'message' => 'Documento legal no encontrado.',
            ], 404);
        }

        $data = $request->validate([
            'consent_type_id' => ['sometimes', 'integer', 'exists:consent_types,id'],
            'version' => ['sometimes', 'string', 'max:50'],
            'published_at' => ['sometimes', 'date'],
            'content_hash' => ['sometimes', 'string', 'size:64'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $targetConsentTypeId = $data['consent_type_id'] ?? $document->consent_type_id;
        $targetVersion = array_key_exists('version', $data)
            ? trim($data['version'])
            : $document->version;

        $exists = LegalDocument::query()
            ->where('consent_type_id', $targetConsentTypeId)
            ->where('version', $targetVersion)
            ->where('id', '!=', $document->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un documento legal con esa versión para el tipo de consentimiento indicado.',
                'errors' => [
                    'version' => [
                        'Ya existe un documento legal con esa versión para el tipo de consentimiento indicado.',
                    ],
                ],
            ], 422);
        }

        if (array_key_exists('consent_type_id', $data)) {
            $document->consent_type_id = $data['consent_type_id'];
        }

        if (array_key_exists('version', $data)) {
            $document->version = trim($data['version']);
        }

        if (array_key_exists('published_at', $data)) {
            $document->published_at = $data['published_at'];
        }

        if (array_key_exists('content_hash', $data)) {
            $document->content_hash = strtolower(trim($data['content_hash']));
        }

        if (array_key_exists('is_active', $data)) {
            $document->is_active = $data['is_active'];
        }

        $document->save();

        return response()->json([
            'message' => 'Documento legal actualizado correctamente.',
            'data' => $document->load('consentType'),
        ], 200);
    }

    public function deleteDocument(int $id): JsonResponse
    {
        $document = LegalDocument::query()->find($id);

        if (!$document) {
            return response()->json([
                'message' => 'Documento legal no encontrado.',
            ], 404);
        }

        $document->delete();

        return response()->json([
            'message' => 'Documento legal eliminado correctamente.',
        ], 200);
    }
}