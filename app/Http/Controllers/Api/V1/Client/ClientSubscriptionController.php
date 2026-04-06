<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientSubscriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $subscriptions = UserSubscriptionPlan::query()
            ->with('subscriptionPlan')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get()
            ->map(function (UserSubscriptionPlan $subscription) {
                return [
                    'id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'subscription_plan_id' => $subscription->subscription_plan_id,
                    'start_date' => optional($subscription->start_date)?->toDateTimeString(),
                    'end_date' => optional($subscription->end_date)?->toDateTimeString(),
                    'is_active' => is_null($subscription->end_date) || $subscription->end_date->isFuture(),
                    'subscription_plan' => $subscription->subscriptionPlan,
                ];
            });

        return response()->json([
            'message' => 'Suscripciones obtenidas correctamente.',
            'data' => $subscriptions,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subscription_plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'start_date' => ['sometimes', 'date'],
        ]);

        $userId = $request->user()->id;

        $activeSubscription = UserSubscriptionPlan::query()
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->first();

        if ($activeSubscription) {
            return response()->json([
                'message' => 'El usuario ya tiene una suscripción activa.',
            ], 422);
        }

        $plan = SubscriptionPlan::query()->find($data['subscription_plan_id']);

        $startDate = isset($data['start_date'])
            ? Carbon::parse($data['start_date'])
            : now();

        $subscription = UserSubscriptionPlan::create([
            'user_id' => $userId,
            'subscription_plan_id' => $plan->id,
            'start_date' => $startDate,
            'end_date' => null,
        ]);

        return response()->json([
            'message' => 'Suscripción creada correctamente.',
            'data' => $subscription->load('subscriptionPlan'),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $subscription = UserSubscriptionPlan::query()
            ->with('subscriptionPlan')
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'Suscripción no encontrada.',
            ], 404);
        }

        return response()->json([
            'message' => 'Suscripción obtenida correctamente.',
            'data' => [
                'id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'subscription_plan_id' => $subscription->subscription_plan_id,
                'start_date' => optional($subscription->start_date)?->toDateTimeString(),
                'end_date' => optional($subscription->end_date)?->toDateTimeString(),
                'is_active' => is_null($subscription->end_date) || $subscription->end_date->isFuture(),
                'subscription_plan' => $subscription->subscriptionPlan,
            ],
        ], 200);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $subscription = UserSubscriptionPlan::query()
            ->with('subscriptionPlan')
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'Suscripción no encontrada.',
            ], 404);
        }

        if (!is_null($subscription->end_date) && !$subscription->end_date->isFuture()) {
            return response()->json([
                'message' => 'La suscripción ya se encuentra finalizada.',
            ], 422);
        }

        $subscription->end_date = now();
        $subscription->save();

        return response()->json([
            'message' => 'Suscripción cancelada correctamente.',
            'data' => [
                'id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'subscription_plan_id' => $subscription->subscription_plan_id,
                'start_date' => optional($subscription->start_date)?->toDateTimeString(),
                'end_date' => optional($subscription->end_date)?->toDateTimeString(),
                'is_active' => false,
                'subscription_plan' => $subscription->subscriptionPlan,
            ],
        ], 200);
    }
}