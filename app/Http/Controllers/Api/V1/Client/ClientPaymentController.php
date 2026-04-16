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
use Illuminate\Validation\Rule;

class ClientPaymentController extends Controller
{
    public function __construct(
        protected AssistanceRequestLifecycleService $lifecycle,
    ) {
    }

    protected function resolvePaymentMethodType(string $code): ?PaymentMethodType
    {
        return PaymentMethodType::query()
            ->where('code', strtolower(trim($code)))
            ->where('is_active', true)
            ->first();
    }

    protected function blockedStatuses(): array
    {
        return ['pending', 'pending_validation', 'completed'];
    }

    public function index(Request $request): JsonResponse
    {
        $payments = Payment::query()
            ->whereHas('assistanceRequest', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with(['assistanceRequest.service', 'assistanceRequest.vehicle', 'transactions'])
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
            'payment_method' => ['required', 'string', Rule::in(['cash', 'transfer'])],
            'reference' => ['nullable', 'string', 'max:120', 'required_if:payment_method,transfer'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $assistanceRequest = AssistanceRequest::query()
            ->with(['payment', 'provider', 'service', 'vehicle'])
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

        $paymentMethodCode = strtolower(trim((string) $data['payment_method']));
        $paymentMethodType = $this->resolvePaymentMethodType($paymentMethodCode);

        if (!$paymentMethodType) {
            return response()->json([
                'message' => 'El método de pago indicado no existe o no está activo.',
            ], 422);
        }

        if (
            $assistanceRequest->payment &&
            in_array(strtolower((string) $assistanceRequest->payment->status), $this->blockedStatuses(), true)
        ) {
            return response()->json([
                'message' => 'La solicitud ya tiene un pago registrado o en proceso de validación.',
                'data' => $assistanceRequest->payment->load(['transactions']),
            ], 422);
        }

        $amount = (float) ($assistanceRequest->final_amount ?? $assistanceRequest->quoted_amount ?? 0);

        if ($amount <= 0) {
            return response()->json([
                'message' => 'La solicitud no tiene un monto final válido para registrar el pago.',
            ], 422);
        }

        $isTransfer = $paymentMethodCode === 'transfer';
        $paymentStatus = $isTransfer ? 'pending_validation' : 'completed';
        $requestPaymentStatus = $isTransfer ? 'pending_validation' : 'paid';
        $reference = filled($data['reference'] ?? null) ? trim((string) $data['reference']) : null;
        $notes = filled($data['notes'] ?? null) ? trim((string) $data['notes']) : null;

        $payment = DB::transaction(function () use (
            $assistanceRequest,
            $request,
            $amount,
            $paymentMethodCode,
            $paymentStatus,
            $requestPaymentStatus,
            $reference,
            $notes,
            $isTransfer
        ) {
            $payment = $assistanceRequest->payment ?? new Payment();
            $payment->assistance_request_id = $assistanceRequest->id;
            $payment->amount = $amount;
            $payment->payment_method = $paymentMethodCode;
            $payment->reference = $reference;
            $payment->notes = $notes;
            $payment->transaction_id = ($isTransfer ? 'TRF-' : 'CASH-') . Str::upper(Str::random(12));
            $payment->status = $paymentStatus;
            $payment->validated_by = null;
            $payment->validated_at = $isTransfer ? null : now();
            $payment->save();

            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'gateway' => $isTransfer ? 'manual_transfer' : 'manual_cash',
                'gateway_event_id' => 'EVT-' . Str::upper(Str::random(16)),
            ]);

            $assistanceRequest->payment_status = $requestPaymentStatus;
            $assistanceRequest->payment_method = $paymentMethodCode;
            $assistanceRequest->final_amount = $amount;
            $assistanceRequest->save();

            $this->lifecycle->createTimelineEntry(
                $assistanceRequest,
                $assistanceRequest->status,
                $isTransfer ? 'payment_submitted_for_validation' : 'payment_registered',
                [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'reference' => $payment->reference,
                    'notes' => $payment->notes,
                    'transaction_id' => $payment->transaction_id,
                    'status' => $payment->status,
                    'request_payment_status' => $assistanceRequest->payment_status,
                ]
            );

            $this->lifecycle->notifyUser(
                $request->user()->id,
                'payment',
                $isTransfer
                    ? 'Tu pago por transferencia fue enviado a validación.'
                    : 'Tu pago fue registrado correctamente.'
            );

            if (!is_null($assistanceRequest->provider?->user_id)) {
                $this->lifecycle->notifyUser(
                    $assistanceRequest->provider->user_id,
                    'payment',
                    $isTransfer
                        ? 'Se registró un pago por transferencia pendiente de validación en un servicio que atendiste.'
                        : 'Se registró el pago de un servicio que atendiste.'
                );
            }

            $this->lifecycle->audit(
                $request->user()->id,
                $isTransfer ? 'client.payment.pending_validation' : 'client.payment.completed',
                [
                    'payment_id' => $payment->id,
                    'assistance_request_id' => $assistanceRequest->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'reference' => $payment->reference,
                    'notes' => $payment->notes,
                    'transaction_id' => $payment->transaction_id,
                    'payment_status' => $payment->status,
                    'request_payment_status' => $assistanceRequest->payment_status,
                ]
            );

            return $payment;
        });

        return response()->json([
            'message' => $isTransfer
                ? 'Pago enviado a validación correctamente.'
                : 'Pago registrado correctamente.',
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
                'reference' => $payment->reference,
                'notes' => $payment->notes,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status,
                'validated_at' => $payment->validated_at,
                'assistance_request' => [
                    'id' => $payment->assistanceRequest?->id,
                    'public_id' => $payment->assistanceRequest?->public_id,
                    'service' => $payment->assistanceRequest?->service?->name,
                    'vehicle' => $payment->assistanceRequest?->vehicle
                        ? trim(($payment->assistanceRequest->vehicle->brand ?? '') . ' ' . ($payment->assistanceRequest->vehicle->model ?? ''))
                        : null,
                    'quoted_amount' => $payment->assistanceRequest?->quoted_amount,
                    'final_amount' => $payment->assistanceRequest?->final_amount,
                    'payment_status' => $payment->assistanceRequest?->payment_status,
                ],
                'issued_at' => $payment->created_at,
            ],
        ], 200);
    }
}
