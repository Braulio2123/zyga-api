<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethodType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodTypeController extends Controller
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

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . trim((string) $request->query('code')) . '%');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . trim((string) $request->query('name')) . '%');
        }

        $paymentMethodTypes = $query->get();

        return response()->json([
            'message' => 'Tipos de método de pago obtenidos correctamente.',
            'data' => $paymentMethodTypes,
        ], 200);
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
}