<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientPaymentMethodController extends Controller
{
    protected function resolveActivePaymentMethodType(string $code): ?PaymentMethodType
    {
        return PaymentMethodType::query()
            ->where('code', strtolower(trim($code)))
            ->where('is_active', true)
            ->first();
    }

    protected function transformPaymentMethod(PaymentMethod $paymentMethod): array
    {
        $type = PaymentMethodType::query()
            ->where('code', $paymentMethod->method_name)
            ->first();

        return [
            'id' => $paymentMethod->id,
            'user_id' => $paymentMethod->user_id,
            'method_name' => $paymentMethod->method_name,
            'method_details' => $paymentMethod->method_details,
            'payment_method_type' => $type ? [
                'id' => $type->id,
                'code' => $type->code,
                'name' => $type->name,
                'is_active' => $type->is_active,
            ] : null,
            'created_at' => $paymentMethod->created_at,
            'updated_at' => $paymentMethod->updated_at,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $paymentMethods = PaymentMethod::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn (PaymentMethod $paymentMethod) => $this->transformPaymentMethod($paymentMethod));

        return response()->json([
            'message' => 'Métodos de pago obtenidos correctamente.',
            'data' => $paymentMethods,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'method_name' => ['required', 'string', 'max:50'],
            'method_details' => ['required', 'string', 'max:255'],
        ]);

        $paymentMethodType = $this->resolveActivePaymentMethodType($data['method_name']);

        if (!$paymentMethodType) {
            return response()->json([
                'message' => 'El tipo de método de pago indicado no existe o no está activo.',
                'errors' => [
                    'method_name' => [
                        'El tipo de método de pago indicado no existe o no está activo.',
                    ],
                ],
            ], 422);
        }

        $paymentMethod = PaymentMethod::create([
            'user_id' => $request->user()->id,
            'method_name' => $paymentMethodType->code,
            'method_details' => trim($data['method_details']),
        ]);

        return response()->json([
            'message' => 'Método de pago registrado correctamente.',
            'data' => $this->transformPaymentMethod($paymentMethod),
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$paymentMethod) {
            return response()->json([
                'message' => 'Método de pago no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Método de pago obtenido correctamente.',
            'data' => $this->transformPaymentMethod($paymentMethod),
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$paymentMethod) {
            return response()->json([
                'message' => 'Método de pago no encontrado.',
            ], 404);
        }

        $data = $request->validate([
            'method_name' => ['sometimes', 'string', 'max:50'],
            'method_details' => ['sometimes', 'string', 'max:255'],
        ]);

        if (array_key_exists('method_name', $data)) {
            $paymentMethodType = $this->resolveActivePaymentMethodType($data['method_name']);

            if (!$paymentMethodType) {
                return response()->json([
                    'message' => 'El tipo de método de pago indicado no existe o no está activo.',
                    'errors' => [
                        'method_name' => [
                            'El tipo de método de pago indicado no existe o no está activo.',
                        ],
                    ],
                ], 422);
            }

            $paymentMethod->method_name = $paymentMethodType->code;
        }

        if (array_key_exists('method_details', $data)) {
            $paymentMethod->method_details = trim($data['method_details']);
        }

        $paymentMethod->save();

        return response()->json([
            'message' => 'Método de pago actualizado correctamente.',
            'data' => $this->transformPaymentMethod($paymentMethod),
        ], 200);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$paymentMethod) {
            return response()->json([
                'message' => 'Método de pago no encontrado.',
            ], 404);
        }

        $paymentMethod->delete();

        return response()->json([
            'message' => 'Método de pago eliminado correctamente.',
        ], 200);
    }
}