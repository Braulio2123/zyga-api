<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientAddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = UserAddress::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Direcciones obtenidas correctamente.',
            'data' => $addresses,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'zip_code' => ['required', 'string', 'max:20'],
        ]);

        $address = UserAddress::create([
            'user_id' => $request->user()->id,
            'address' => trim($data['address']),
            'city' => trim($data['city']),
            'state' => trim($data['state']),
            'country' => trim($data['country']),
            'zip_code' => trim($data['zip_code']),
        ]);

        return response()->json([
            'message' => 'Dirección registrada correctamente.',
            'data' => $address,
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $address = UserAddress::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json([
                'message' => 'Dirección no encontrada.',
            ], 404);
        }

        return response()->json([
            'message' => 'Dirección obtenida correctamente.',
            'data' => $address,
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $address = UserAddress::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json([
                'message' => 'Dirección no encontrada.',
            ], 404);
        }

        $data = $request->validate([
            'address' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:100'],
            'state' => ['sometimes', 'string', 'max:100'],
            'country' => ['sometimes', 'string', 'max:100'],
            'zip_code' => ['sometimes', 'string', 'max:20'],
        ]);

        if (array_key_exists('address', $data)) {
            $address->address = trim($data['address']);
        }

        if (array_key_exists('city', $data)) {
            $address->city = trim($data['city']);
        }

        if (array_key_exists('state', $data)) {
            $address->state = trim($data['state']);
        }

        if (array_key_exists('country', $data)) {
            $address->country = trim($data['country']);
        }

        if (array_key_exists('zip_code', $data)) {
            $address->zip_code = trim($data['zip_code']);
        }

        $address->save();

        return response()->json([
            'message' => 'Dirección actualizada correctamente.',
            'data' => $address,
        ], 200);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $address = UserAddress::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json([
                'message' => 'Dirección no encontrada.',
            ], 404);
        }

        $address->delete();

        return response()->json([
            'message' => 'Dirección eliminada correctamente.',
        ], 200);
    }
}