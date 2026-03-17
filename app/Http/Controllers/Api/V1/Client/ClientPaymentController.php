<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $payments = Payment::query()
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['assistanceRequest.service', 'assistanceRequest.vehicle'])
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Pagos obtenidos correctamente.',
            'data' => $payments,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'La integración de pago real aún no está implementada.',
        ], 501);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $payment = Payment::query()
            ->where('id', $id)
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['assistanceRequest.service', 'assistanceRequest.vehicle'])
            ->first();

        if (!$payment) {
            return response()->json([
                'message' => 'Pago no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Pago obtenido correctamente.',
            'data' => $payment,
        ], 200);
    }

    public function receipt(Request $request, string $id): JsonResponse
    {
        $payment = Payment::query()
            ->where('id', $id)
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['assistanceRequest.service', 'assistanceRequest.vehicle'])
            ->first();

        if (!$payment) {
            return response()->json([
                'message' => 'Pago no encontrado.',
            ], 404);
        }

        return response()->json([
            'message' => 'Recibo de pago obtenido correctamente.',
            'data' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status,
                'assistance_request' => [
                    'id' => $payment->assistanceRequest?->id,
                    'public_id' => $payment->assistanceRequest?->public_id,
                    'service' => $payment->assistanceRequest?->service?->name,
                    'vehicle' => $payment->assistanceRequest?->vehicle
                        ? trim(($payment->assistanceRequest->vehicle->brand ?? '') . ' ' . ($payment->assistanceRequest->vehicle->model ?? ''))
                        : null,
                ],
                'issued_at' => $payment->created_at,
            ],
        ], 200);
    }
}