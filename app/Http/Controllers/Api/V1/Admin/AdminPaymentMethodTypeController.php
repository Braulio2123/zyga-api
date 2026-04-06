<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethodType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPaymentMethodTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PaymentMethodType::query()->orderBy('id');

        if ($request->has('is_active')) {
            $query->where(
                'is_active',
                filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN)
            );
        }

        $paymentMethodTypes = $query->get();

        return response()->json([
            'message' => 'Tipos de método de pago obtenidos correctamente.',
            'data' => $paymentMethodTypes,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:payment_method_types,code'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $paymentMethodType = PaymentMethodType::create([
            'code' => strtolower(trim($data['code'])),
            'name' => trim($data['name']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Tipo de método de pago creado correctamente.',
            'data' => $paymentMethodType,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $paymentMethodType = PaymentMethodType::query()->find($id);

        if (!$paymentMethodType) {
            return response()->json([
                'message' => 'Tipo de método de pago no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Tipo de método de pago obtenido correctamente.',
            'data' => $paymentMethodType,
        ], 200);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $paymentMethodType = PaymentMethodType::query()->find($id);

        if (!$paymentMethodType) {
            return response()->json([
                'message' => 'Tipo de método de pago no encontrado.',
            ], 404);
        }

        $data = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('payment_method_types', 'code')->ignore($paymentMethodType->id),
            ],
            'name' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('code', $data)) {
            $paymentMethodType->code = strtolower(trim($data['code']));
        }

        if (array_key_exists('name', $data)) {
            $paymentMethodType->name = trim($data['name']);
        }

        if (array_key_exists('is_active', $data)) {
            $paymentMethodType->is_active = $data['is_active'];
        }

        $paymentMethodType->save();

        return response()->json([
            'message' => 'Tipo de método de pago actualizado correctamente.',
            'data' => $paymentMethodType,
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $paymentMethodType = PaymentMethodType::query()->find($id);

        if (!$paymentMethodType) {
            return response()->json([
                'message' => 'Tipo de método de pago no encontrado.',
            ], 404);
        }

        $paymentMethodType->delete();

        return response()->json([
            'message' => 'Tipo de método de pago eliminado correctamente.',
        ], 200);
    }
}