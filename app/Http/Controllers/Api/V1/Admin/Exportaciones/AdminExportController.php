<?php

namespace App\Http\Controllers\Api\V1\Admin\Exportaciones;

use App\Exports\AdminArrayExport;
use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminExportController extends Controller
{
    public function usersExcel(Request $request)
    {
        [$headings, $rows, $filters] = $this->buildUsersData($request);

        return Excel::download(
            new AdminArrayExport($headings, $rows),
            'usuarios_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function usersPdf(Request $request)
    {
        [$headings, $rows, $filters] = $this->buildUsersData($request);

        $pdf = Pdf::loadView('admin.exports.report', [
            'title' => 'Reporte de usuarios',
            'headings' => $headings,
            'rows' => $rows,
            'filters' => $filters,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('usuarios_' . now()->format('Ymd_His') . '.pdf');
    }

    public function providersExcel(Request $request)
    {
        [$headings, $rows, $filters] = $this->buildProvidersData($request);

        return Excel::download(
            new AdminArrayExport($headings, $rows),
            'proveedores_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function providersPdf(Request $request)
    {
        [$headings, $rows, $filters] = $this->buildProvidersData($request);

        $pdf = Pdf::loadView('admin.exports.report', [
            'title' => 'Reporte de proveedores',
            'headings' => $headings,
            'rows' => $rows,
            'filters' => $filters,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('proveedores_' . now()->format('Ymd_His') . '.pdf');
    }

    public function assistanceRequestsExcel(Request $request)
    {
        [$headings, $rows, $filters] = $this->buildAssistanceRequestsData($request);

        return Excel::download(
            new AdminArrayExport($headings, $rows),
            'solicitudes_asistencia_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function assistanceRequestsPdf(Request $request)
    {
        [$headings, $rows, $filters] = $this->buildAssistanceRequestsData($request);

        $pdf = Pdf::loadView('admin.exports.report', [
            'title' => 'Reporte de solicitudes de asistencia',
            'headings' => $headings,
            'rows' => $rows,
            'filters' => $filters,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('solicitudes_asistencia_' . now()->format('Ymd_His') . '.pdf');
    }

    public function paymentsExcel(Request $request)
    {
        [$headings, $rows, $filters] = $this->buildPaymentsData($request);

        return Excel::download(
            new AdminArrayExport($headings, $rows),
            'pagos_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function paymentsPdf(Request $request)
    {
        [$headings, $rows, $filters] = $this->buildPaymentsData($request);

        $pdf = Pdf::loadView('admin.exports.report', [
            'title' => 'Reporte de pagos',
            'headings' => $headings,
            'rows' => $rows,
            'filters' => $filters,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('pagos_' . now()->format('Ymd_His') . '.pdf');
    }

    private function buildUsersData(Request $request): array
    {
        $query = User::query()
            ->with(['roles', 'provider'])
            ->orderByDesc('id');

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . trim((string) $request->query('email')) . '%');
        }

        if ($request->filled('role')) {
            $roleCode = trim((string) $request->query('role'));

            $query->whereHas('roles', function ($q) use ($roleCode) {
                $q->where('code', $roleCode);
            });
        }

        $items = $query->get();

        $headings = [
            'ID',
            'Correo',
            'Roles',
            'Proveedor vinculado',
            'Fecha de registro',
        ];

        $rows = $items->map(function (User $user) {
            return [
                $user->id,
                $user->email,
                $user->roles->pluck('code')->implode(', '),
                $user->provider?->display_name ?? 'No',
                optional($user->created_at)->format('Y-m-d H:i:s'),
            ];
        })->values()->all();

        $filters = [
            'email' => $request->query('email'),
            'role' => $request->query('role'),
        ];

        return [$headings, $rows, $filters];
    }

    private function buildProvidersData(Request $request): array
    {
        $query = Provider::query()
            ->with(['user', 'status', 'services'])
            ->orderByDesc('id');

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->query('status_id'));
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', filter_var($request->query('is_verified'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('email')) {
            $email = trim((string) $request->query('email'));

            $query->whereHas('user', function ($q) use ($email) {
                $q->where('email', 'like', '%' . $email . '%');
            });
        }

        if ($request->filled('service_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('services.id', $request->query('service_id'));
            });
        }

        $items = $query->get();

        $headings = [
            'ID',
            'Nombre visible',
            'Correo',
            'Tipo',
            'Estado',
            'Verificado',
            'Servicios',
            'Fecha de registro',
        ];

        $rows = $items->map(function (Provider $provider) {
            return [
                $provider->id,
                $provider->display_name,
                $provider->user?->email ?? '',
                $provider->provider_kind ?? '',
                $provider->status?->name ?? ($provider->status?->code ?? ''),
                $provider->is_verified ? 'Sí' : 'No',
                $provider->services->pluck('name')->implode(', '),
                optional($provider->created_at)->format('Y-m-d H:i:s'),
            ];
        })->values()->all();

        $filters = [
            'status_id' => $request->query('status_id'),
            'is_verified' => $request->query('is_verified'),
            'email' => $request->query('email'),
            'service_id' => $request->query('service_id'),
        ];

        return [$headings, $rows, $filters];
    }

    private function buildAssistanceRequestsData(Request $request): array
    {
        $query = AssistanceRequest::query()
            ->with([
                'user',
                'provider',
                'provider.user',
                'service',
                'vehicle',
            ])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->query('provider_id'));
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->query('service_id'));
        }

        if ($request->filled('public_id')) {
            $query->where('public_id', $request->query('public_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        $items = $query->get();

        $headings = [
            'ID',
            'Folio',
            'Cliente',
            'Proveedor',
            'Servicio',
            'Vehículo',
            'Estado',
            'Pago',
            'Método de pago',
            'Monto cotizado',
            'Monto final',
            'Dirección',
            'Fecha de registro',
        ];

        $rows = $items->map(function (AssistanceRequest $requestItem) {
            $vehicleLabel = trim(implode(' ', array_filter([
                $requestItem->vehicle?->brand,
                $requestItem->vehicle?->model,
                $requestItem->vehicle?->plate,
            ])));

            return [
                $requestItem->id,
                $requestItem->public_id,
                $requestItem->user?->email ?? '',
                $requestItem->provider?->display_name ?? ($requestItem->provider?->user?->email ?? ''),
                $requestItem->service?->name ?? '',
                $vehicleLabel,
                $requestItem->status,
                $requestItem->payment_status,
                $requestItem->payment_method,
                $requestItem->quoted_amount,
                $requestItem->final_amount,
                $requestItem->pickup_address,
                optional($requestItem->created_at)->format('Y-m-d H:i:s'),
            ];
        })->values()->all();

        $filters = [
            'status' => $request->query('status'),
            'user_id' => $request->query('user_id'),
            'provider_id' => $request->query('provider_id'),
            'service_id' => $request->query('service_id'),
            'public_id' => $request->query('public_id'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
        ];

        return [$headings, $rows, $filters];
    }

    private function buildPaymentsData(Request $request): array
    {
        $query = Payment::query()
            ->with(['assistanceRequest', 'validator'])
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

        $items = $query->get();

        $headings = [
            'ID',
            'Solicitud',
            'Monto',
            'Método de pago',
            'Referencia',
            'Transacción',
            'Estado',
            'Validado por',
            'Fecha de validación',
            'Fecha de registro',
        ];

        $rows = $items->map(function (Payment $payment) {
            return [
                $payment->id,
                $payment->assistanceRequest?->public_id ?? $payment->assistance_request_id,
                $payment->amount,
                $payment->payment_method,
                $payment->reference,
                $payment->transaction_id,
                $payment->status,
                $payment->validator?->email ?? '',
                optional($payment->validated_at)->format('Y-m-d H:i:s'),
                optional($payment->created_at)->format('Y-m-d H:i:s'),
            ];
        })->values()->all();

        $filters = [
            'assistance_request_id' => $request->query('assistance_request_id'),
            'status' => $request->query('status'),
            'payment_method' => $request->query('payment_method'),
            'transaction_id' => $request->query('transaction_id'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
        ];

        return [$headings, $rows, $filters];
    }
}