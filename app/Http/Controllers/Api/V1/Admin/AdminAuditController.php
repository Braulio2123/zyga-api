<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AuditLog::query()
            ->with('user')
            ->orderByDesc('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . trim((string) $request->query('action')) . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        $auditLogs = $query->get();

        return response()->json([
            'message' => 'Registros de auditoría obtenidos correctamente.',
            'data' => $auditLogs,
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $auditLog = AuditLog::query()
            ->with('user')
            ->find($id);

        if (!$auditLog) {
            return response()->json([
                'message' => 'Registro de auditoría no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Registro de auditoría obtenido correctamente.',
            'data' => $auditLog,
        ], 200);
    }
}