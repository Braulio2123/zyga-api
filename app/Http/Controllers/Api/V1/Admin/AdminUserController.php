<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()
            ->with('roles')
            ->orderByDesc('id');

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . trim((string) $request->query('email')) . '%');
        }

        if ($request->filled('role')) {
            $roleCode = trim((string) $request->query('role'));

            $query->whereHas('roles', function ($q) use ($roleCode) {
                $q->where('code', $roleCode);
            });
        }

        $users = $query->get();

        return response()->json([
            'message' => 'Usuarios obtenidos correctamente.',
            'data' => $users,
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::query()
            ->with(['roles', 'provider', 'vehicles', 'assistanceRequests'])
            ->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Usuario obtenido correctamente.',
            'data' => $user,
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::query()->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        $original = [
            'email' => $user->email,
        ];

        $data = $request->validate([
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['sometimes', 'string', 'min:8', 'max:255'],
        ]);

        $changedFields = [];

        if (array_key_exists('email', $data)) {
            $newEmail = trim(strtolower($data['email']));

            if ($newEmail !== $user->email) {
                $user->email = $newEmail;
                $changedFields[] = 'email';
            }
        }

        if (array_key_exists('password', $data)) {
            $user->password = $data['password'];
            $changedFields[] = 'password';
        }

        if (empty($changedFields)) {
            return response()->json([
                'message' => 'No se detectaron cambios para actualizar.',
                'data' => $user->load('roles'),
            ], 200);
        }

        $user->save();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.user.updated',
            'description' => json_encode([
                'target_user_id' => $user->id,
                'changed_fields' => $changedFields,
                'before' => $original,
                'after' => [
                    'email' => $user->email,
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
            'data' => $user->load('roles'),
        ], 200);
    }
}