<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminFinanceController extends Controller
{
    public function payments(Request $request): JsonResponse
    {
        $query = Payment::query()
            ->with(['assistanceRequest', 'transactions', 'validator'])
            ->orderByDesc('id');

        if ($request->filled('assistance_request_id')) {
            $query->where('assistance_request_id', $request->query('assistance_request_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', 'like', '%' . trim((string) $request->query('payment_method')) . '%');
        }

        if ($request->filled('transaction_id')) {
            $query->where('transaction_id', 'like', '%' . trim((string) $request->query('transaction_id')) . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        $payments = $query->get();

        return response()->json([
            'message' => 'Pagos obtenidos correctamente.',
            'data' => $payments,
        ], 200);
    }

    public function showPayment(int $id): JsonResponse
    {
        $payment = Payment::query()
            ->with(['assistanceRequest', 'transactions', 'validator'])
            ->find($id);

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

    public function updatePayment(Request $request, int $id): JsonResponse
    {
        $payment = Payment::query()
            ->with('assistanceRequest')
            ->find($id);

        if (!$payment) {
            return response()->json([
                'message' => 'Pago no encontrado.',
            ], 404);
        }

        $original = [
            'payment_method' => $payment->payment_method,
            'reference' => $payment->reference,
            'notes' => $payment->notes,
            'transaction_id' => $payment->transaction_id,
            'status' => $payment->status,
            'validated_by' => $payment->validated_by,
            'validated_at' => $payment->validated_at,
            'request_payment_status' => $payment->assistanceRequest?->payment_status,
        ];

        $data = $request->validate([
            'payment_method' => ['sometimes', 'string', 'max:50'],
            'reference' => ['sometimes', 'nullable', 'string', 'max:120'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'transaction_id' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('payments', 'transaction_id')->ignore($payment->id),
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['pending', 'pending_validation', 'completed', 'failed', 'rejected']),
            ],
        ]);

        if (empty($data)) {
            return response()->json([
                'message' => 'No se enviaron campos válidos para actualizar.',
            ], 422);
        }

        $changedFields = [];

        if (array_key_exists('payment_method', $data) && $data['payment_method'] !== $payment->payment_method) {
            $payment->payment_method = trim((string) $data['payment_method']);
            $changedFields[] = 'payment_method';
        }

        if (array_key_exists('reference', $data) && $data['reference'] !== $payment->reference) {
            $payment->reference = filled($data['reference']) ? trim((string) $data['reference']) : null;
            $changedFields[] = 'reference';
        }

        if (array_key_exists('notes', $data) && $data['notes'] !== $payment->notes) {
            $payment->notes = filled($data['notes']) ? trim((string) $data['notes']) : null;
            $changedFields[] = 'notes';
        }

        if (array_key_exists('transaction_id', $data) && $data['transaction_id'] !== $payment->transaction_id) {
            $payment->transaction_id = trim((string) $data['transaction_id']);
            $changedFields[] = 'transaction_id';
        }

        if (array_key_exists('status', $data) && $data['status'] !== $payment->status) {
            $payment->status = $data['status'];
            $changedFields[] = 'status';

            if (in_array($payment->status, ['completed', 'rejected'], true)) {
                $payment->validated_by = $request->user()->id;
                $payment->validated_at = now();
            } else {
                $payment->validated_by = null;
                $payment->validated_at = null;
            }
        }

        if (empty($changedFields)) {
            return response()->json([
                'message' => 'No se detectaron cambios para actualizar.',
                'data' => $payment->load(['assistanceRequest', 'transactions', 'validator']),
            ], 200);
        }

        $payment->save();

        if ($payment->assistanceRequest) {
            if (in_array('payment_method', $changedFields, true)) {
                $payment->assistanceRequest->payment_method = $payment->payment_method;
            }

            if (in_array('status', $changedFields, true)) {
                $payment->assistanceRequest->payment_status = match ($payment->status) {
                    'completed' => 'paid',
                    'pending_validation' => 'pending_validation',
                    'pending' => 'pending',
                    'failed' => 'failed',
                    'rejected' => 'rejected',
                    default => $payment->assistanceRequest->payment_status,
                };
            }

            $payment->assistanceRequest->save();
        }

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.payment.updated',
            'description' => json_encode([
                'payment_id' => $payment->id,
                'assistance_request_id' => $payment->assistance_request_id,
                'changed_fields' => $changedFields,
                'before' => $original,
                'after' => [
                    'payment_method' => $payment->payment_method,
                    'reference' => $payment->reference,
                    'notes' => $payment->notes,
                    'transaction_id' => $payment->transaction_id,
                    'status' => $payment->status,
                    'validated_by' => $payment->validated_by,
                    'validated_at' => $payment->validated_at,
                    'request_payment_status' => $payment->assistanceRequest?->payment_status,
                ],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json([
            'message' => 'Pago actualizado correctamente.',
            'data' => $payment->load(['assistanceRequest', 'transactions', 'validator']),
        ], 200);
    }

    public function transactions(Request $request): JsonResponse
    {
        $query = PaymentTransaction::query()
            ->with('payment')
            ->orderByDesc('id');

        if ($request->filled('payment_id')) {
            $query->where('payment_id', $request->query('payment_id'));
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', 'like', '%' . trim((string) $request->query('gateway')) . '%');
        }

        if ($request->filled('gateway_event_id')) {
            $query->where('gateway_event_id', 'like', '%' . trim((string) $request->query('gateway_event_id')) . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        $transactions = $query->get();

        return response()->json([
            'message' => 'Transacciones de pago obtenidas correctamente.',
            'data' => $transactions,
        ], 200);
    }

    public function showTransaction(int $id): JsonResponse
    {
        $transaction = PaymentTransaction::query()
            ->with('payment')
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transacción de pago no encontrada.',
            ], 404);
        }

        return response()->json([
            'message' => 'Transacción de pago obtenida correctamente.',
            'data' => $transaction,
        ], 200);
    }
}
