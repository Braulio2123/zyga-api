<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'provider']);

        return response()->json([
            'message' => 'Perfil obtenido correctamente.',
            'data' => [
                'user' => $user,
                'roles' => $user->roles->map(fn ($role) => [
                    'id' => $role->id,
                    'code' => $role->code,
                    'name' => $role->name,
                ])->values(),
                'provider_profile' => $user->provider,
            ],
        ], 200);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['sometimes', 'string', 'min:8', 'max:255'],
        ]);

        if (array_key_exists('email', $data)) {
            $user->email = $data['email'];
        }

        if (array_key_exists('password', $data)) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $user->load(['roles', 'provider']);

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'data' => [
                'user' => $user,
                'roles' => $user->roles->map(fn ($role) => [
                    'id' => $role->id,
                    'code' => $role->code,
                    'name' => $role->name,
                ])->values(),
                'provider_profile' => $user->provider,
            ],
        ], 200);
    }
}