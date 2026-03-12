<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Models\UserSetting;
use App\Models\UserSubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'provider']);

        $addresses = UserAddress::where('user_id', $user->id)->get();
        $settings = UserSetting::where('user_id', $user->id)->get();
        $subscription = UserSubscriptionPlan::with('subscriptionPlan')
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        return response()->json([
            'data' => [
                'user' => $user,
                'roles' => $user->roles->map(fn ($role) => [
                    'id' => $role->id,
                    'code' => $role->code,
                    'name' => $role->name,
                ])->values(),
                'provider_profile' => $user->provider,
                'addresses' => $addresses,
                'settings' => $settings,
                'subscription' => $subscription,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        if (array_key_exists('email', $data)) {
            $user->email = $data['email'];
        }

        if (array_key_exists('password', $data)) {
            $user->password = $data['password'];
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
        ]);
    }
}
