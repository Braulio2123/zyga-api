<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Payment;
use App\Models\PaymentMethodType;
use App\Models\PaymentTransaction;
use App\Services\AssistanceRequestLifecycleService;
use App\Support\AssistanceRequestFlow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientPaymentController extends Controller
{
    public function __construct(
        protected AssistanceRequestLifecycleService $lifecycle,
    ) {
    }

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
        $data = $request->validate([
            'assistance_request_id' => ['required', 'integer', 'exists:assistance_requests,id'],
            'payment_method' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $assistanceRequest = AssistanceRequest::query()
            ->with(['payment', 'provider'])
            ->where('id', $data['assistance_request_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$assistanceRequest) {
            return response()->json([
                'message' => 'La solicitud de asistencia indicada no pertenece al usuario autenticado.',
            ], 404);
        }

        if ($assistanceRequest->status !== AssistanceRequestFlow::COMPLETED) {
            return response()->json([
                'message' => 'Solo se puede registrar el pago de una solicitud completada.',
                'data' => [
                    'current_status' => $assistanceRequest->status,
                ],
            ], 422);
        }

        $paymentMethodType = PaymentMethodType::query()
            ->where('code', strtolower(trim($data['payment_method'])))
            ->where('is_active', true)
            ->first();

        if (!$paymentMethodType) {
            return response()->json([
                'message' => 'El método de pago indicado no existe o no está activo.',
            ], 422);
        }

        if ($assistanceRequest->payment && $assistanceRequest->payment->status === 'completed') {
            return response()->json([
                'message' => 'La solicitud ya tiene un pago completado.',
                'data' => $assistanceRequest->payment,
            ], 422);
        }

        $payment = DB::transaction(function () use ($assistanceRequest, $paymentMethodType, $data, $request) {
            $payment = $assistanceRequest->payment ?? new Payment();
            $payment->assistance_request_id = $assistanceRequest->id;
            $payment->amount = $data['amount'];
            $payment->payment_method = $paymentMethodType->code;
            $payment->transaction_id = 'PAY-' . Str::upper(Str::random(12));
            $payment->status = 'completed';
            $payment->save();

            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'gateway' => 'sandbox',
                'gateway_event_id' => 'EVT-' . Str::upper(Str::random(16)),
            ]);

            $this->lifecycle->createTimelineEntry(
                $assistanceRequest,
                $assistanceRequest->status,
                'payment_registered',
                [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'transaction_id' => $payment->transaction_id,
                    'status' => $payment->status,
                ]
            );

            $this->lifecycle->notifyUser(
                $request->user()->id,
                'payment',
                'Tu pago fue registrado correctamente.'
            );

            if (!is_null($assistanceRequest->provider?->user_id)) {
                $this->lifecycle->notifyUser(
                    $assistanceRequest->provider->user_id,
                    'payment',
                    'Se registró el pago de un servicio que atendiste.'
                );
            }

            $this->lifecycle->audit(
                $request->user()->id,
                'client.payment.completed',
                [
                    'payment_id' => $payment->id,
                    'assistance_request_id' => $assistanceRequest->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'transaction_id' => $payment->transaction_id,
                ]
            );

            return $payment;
        });

        return response()->json([
            'message' => 'Pago registrado correctamente.',
            'data' => $payment->load(['assistanceRequest.service', 'assistanceRequest.vehicle', 'transactions']),
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $payment = Payment::query()
            ->where('id', $id)
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['assistanceRequest.service', 'assistanceRequest.vehicle', 'transactions'])
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
