<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SubscriptionPlan::query()->orderBy('id');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . trim((string) $request->query('name')) . '%');
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->query('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->query('price_max'));
        }

        $plans = $query->get();

        return response()->json([
            'message' => 'Planes de suscripción obtenidos correctamente.',
            'data' => $plans,
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $plan = SubscriptionPlan::query()->find($id);

        if (!$plan) {
            return response()->json([
                'message' => 'Plan de suscripción no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Plan de suscripción obtenido correctamente.',
            'data' => $plan,
        ], 200);
    }
}