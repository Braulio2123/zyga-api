<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderReviewController extends Controller
{
    protected function resolveProvider(Request $request): ?Provider
    {
        return Provider::query()
            ->where('user_id', $request->user()->id)
            ->first();
    }

    public function index(Request $request): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $reviewsQuery = ProviderReview::query()
            ->where('provider_id', $provider->id)
            ->latest();

        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', (int) $request->query('rating'));
        }

        $reviews = $reviewsQuery->get();

        $summary = [
            'total_reviews' => $reviews->count(),
            'average_rating' => $reviews->count() > 0
                ? round($reviews->avg('rating'), 2)
                : null,
        ];

        return response()->json([
            'message' => 'Reseñas del proveedor obtenidas correctamente.',
            'data' => [
                'provider_id' => $provider->id,
                'display_name' => $provider->display_name,
                'summary' => $summary,
                'reviews' => $reviews,
            ],
        ], 200);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $provider = $this->resolveProvider($request);

        if (!$provider) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene perfil de proveedor registrado.',
            ], 404);
        }

        $review = ProviderReview::query()
            ->where('provider_id', $provider->id)
            ->where('id', $id)
            ->first();

        if (!$review) {
            return response()->json([
                'message' => 'Reseña no encontrada para el proveedor autenticado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Reseña obtenida correctamente.',
            'data' => $review,
        ], 200);
    }
}