<?php

use App\Http\Controllers\Api\V1\Admin\AdminAssistanceController;
use App\Http\Controllers\Api\V1\Admin\AdminAuditController;
use App\Http\Controllers\Api\V1\Admin\AdminFinanceController;
use App\Http\Controllers\Api\V1\Admin\AdminLegalController;
use App\Http\Controllers\Api\V1\Admin\AdminPaymentMethodTypeController;
use App\Http\Controllers\Api\V1\Admin\AdminProviderController;
use App\Http\Controllers\Api\V1\Admin\AdminServiceCatalogController;
use App\Http\Controllers\Api\V1\Admin\AdminStatusController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\Admin\AdminVehicleTypeController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Client\ClientAddressController;
use App\Http\Controllers\Api\V1\Client\ClientAssistanceRequestController;
use App\Http\Controllers\Api\V1\Client\ClientPaymentController;
use App\Http\Controllers\Api\V1\Client\ClientPaymentMethodController;
use App\Http\Controllers\Api\V1\Client\ClientServiceRequestController;
use App\Http\Controllers\Api\V1\Client\ClientSubscriptionController;
use App\Http\Controllers\Api\V1\Client\ClientVehicleController;
use App\Http\Controllers\Api\V1\Common\NotificationController;
use App\Http\Controllers\Api\V1\Common\ProfileController;
use App\Http\Controllers\Api\V1\Provider\ProviderAssistanceController;
use App\Http\Controllers\Api\V1\Provider\ProviderDocumentController;
use App\Http\Controllers\Api\V1\Provider\ProviderProfileController;
use App\Http\Controllers\Api\V1\Provider\ProviderReviewController;
use App\Http\Controllers\Api\V1\Provider\ProviderScheduleController;
use App\Http\Controllers\Api\V1\Provider\ProviderServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTENTICACIÓN
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS AUTENTICADAS
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ACCESO COMÚN
        |--------------------------------------------------------------------------
        */
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        });

        Route::prefix('me')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::patch('/', [ProfileController::class, 'update']);
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/{id}', [NotificationController::class, 'show']);
            Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::patch('/read-all', [NotificationController::class, 'markAllAsRead']);
        });

        /*
        |--------------------------------------------------------------------------
        | CLIENTE
        |--------------------------------------------------------------------------
        | Recomendado después proteger con middleware role:client
        |--------------------------------------------------------------------------
        */
        Route::prefix('client')->group(function () {

            // Vehículos
            Route::get('/vehicles', [ClientVehicleController::class, 'index']);
            Route::post('/vehicles', [ClientVehicleController::class, 'store']);
            Route::get('/vehicles/{id}', [ClientVehicleController::class, 'show']);
            Route::put('/vehicles/{id}', [ClientVehicleController::class, 'update']);
            Route::patch('/vehicles/{id}', [ClientVehicleController::class, 'update']);
            Route::delete('/vehicles/{id}', [ClientVehicleController::class, 'destroy']);

            // Direcciones
            Route::get('/addresses', [ClientAddressController::class, 'index']);
            Route::post('/addresses', [ClientAddressController::class, 'store']);
            Route::get('/addresses/{id}', [ClientAddressController::class, 'show']);
            Route::put('/addresses/{id}', [ClientAddressController::class, 'update']);
            Route::patch('/addresses/{id}', [ClientAddressController::class, 'update']);
            Route::delete('/addresses/{id}', [ClientAddressController::class, 'destroy']);

            // Métodos de pago del cliente
            Route::get('/payment-methods', [ClientPaymentMethodController::class, 'index']);
            Route::post('/payment-methods', [ClientPaymentMethodController::class, 'store']);
            Route::get('/payment-methods/{id}', [ClientPaymentMethodController::class, 'show']);
            Route::put('/payment-methods/{id}', [ClientPaymentMethodController::class, 'update']);
            Route::patch('/payment-methods/{id}', [ClientPaymentMethodController::class, 'update']);
            Route::delete('/payment-methods/{id}', [ClientPaymentMethodController::class, 'destroy']);

            // Solicitudes de asistencia
            Route::get('/assistance-requests', [ClientAssistanceRequestController::class, 'index']);
            Route::post('/assistance-requests', [ClientAssistanceRequestController::class, 'store']);
            Route::get('/assistance-requests/{id}', [ClientAssistanceRequestController::class, 'show']);
            Route::put('/assistance-requests/{id}', [ClientAssistanceRequestController::class, 'update']);
            Route::patch('/assistance-requests/{id}', [ClientAssistanceRequestController::class, 'update']);
            Route::delete('/assistance-requests/{id}', [ClientAssistanceRequestController::class, 'destroy']);

            // Acciones específicas de solicitudes
            Route::patch('/assistance-requests/{id}/cancel', [ClientAssistanceRequestController::class, 'cancel']);
            Route::get('/assistance-requests/{id}/status', [ClientAssistanceRequestController::class, 'status']);
            Route::get('/assistance-requests/{id}/timeline', [ClientAssistanceRequestController::class, 'timeline']);

            // Solicitud de servicios / catálogo operativo
            Route::get('/service-requests', [ClientServiceRequestController::class, 'index']);
            Route::post('/service-requests', [ClientServiceRequestController::class, 'store']);
            Route::get('/service-requests/{id}', [ClientServiceRequestController::class, 'show']);
            Route::patch('/service-requests/{id}/quote', [ClientServiceRequestController::class, 'quote']);
            Route::patch('/service-requests/{id}/confirm', [ClientServiceRequestController::class, 'confirm']);

            // Pagos del cliente
            Route::get('/payments', [ClientPaymentController::class, 'index']);
            Route::post('/payments', [ClientPaymentController::class, 'store']);
            Route::get('/payments/{id}', [ClientPaymentController::class, 'show']);
            Route::get('/payments/{id}/receipt', [ClientPaymentController::class, 'receipt']);

            // Suscripción
            Route::get('/subscription', [ClientSubscriptionController::class, 'show']);
            Route::post('/subscription', [ClientSubscriptionController::class, 'store']);
            Route::patch('/subscription/cancel', [ClientSubscriptionController::class, 'cancel']);
            Route::get('/subscription/history', [ClientSubscriptionController::class, 'history']);
        });

        /*
        |--------------------------------------------------------------------------
        | PROVEEDOR
        |--------------------------------------------------------------------------
        | Recomendado después proteger con middleware role:provider
        |--------------------------------------------------------------------------
        */
        Route::prefix('provider')->group(function () {

            // Perfil del proveedor
            Route::get('/profile', [ProviderProfileController::class, 'show']);
            Route::post('/profile', [ProviderProfileController::class, 'store']);
            Route::put('/profile', [ProviderProfileController::class, 'update']);
            Route::patch('/profile', [ProviderProfileController::class, 'update']);

            // Horarios / disponibilidad
            Route::get('/schedules', [ProviderScheduleController::class, 'index']);
            Route::post('/schedules', [ProviderScheduleController::class, 'store']);
            Route::get('/schedules/{id}', [ProviderScheduleController::class, 'show']);
            Route::put('/schedules/{id}', [ProviderScheduleController::class, 'update']);
            Route::patch('/schedules/{id}', [ProviderScheduleController::class, 'update']);
            Route::delete('/schedules/{id}', [ProviderScheduleController::class, 'destroy']);
            Route::patch('/availability', [ProviderScheduleController::class, 'setAvailability']);

            // Documentos del proveedor
            Route::get('/documents', [ProviderDocumentController::class, 'index']);
            Route::post('/documents', [ProviderDocumentController::class, 'store']);
            Route::get('/documents/{id}', [ProviderDocumentController::class, 'show']);
            Route::delete('/documents/{id}', [ProviderDocumentController::class, 'destroy']);
            Route::patch('/documents/{id}/submit', [ProviderDocumentController::class, 'submit']);

            // Servicios del proveedor
            Route::get('/services', [ProviderServiceController::class, 'index']);
            Route::post('/services', [ProviderServiceController::class, 'store']);
            Route::get('/services/{id}', [ProviderServiceController::class, 'show']);
            Route::put('/services/{id}', [ProviderServiceController::class, 'update']);
            Route::patch('/services/{id}', [ProviderServiceController::class, 'update']);
            Route::delete('/services/{id}', [ProviderServiceController::class, 'destroy']);
            Route::patch('/services/{id}/toggle-status', [ProviderServiceController::class, 'toggleStatus']);

            // Asistencias asignadas / operación
            Route::get('/assistances', [ProviderAssistanceController::class, 'index']);
            Route::get('/assistances/{id}', [ProviderAssistanceController::class, 'show']);
            Route::patch('/assistances/{id}/accept', [ProviderAssistanceController::class, 'accept']);
            Route::patch('/assistances/{id}/reject', [ProviderAssistanceController::class, 'reject']);
            Route::patch('/assistances/{id}/arrived', [ProviderAssistanceController::class, 'arrived']);
            Route::patch('/assistances/{id}/start', [ProviderAssistanceController::class, 'start']);
            Route::patch('/assistances/{id}/complete', [ProviderAssistanceController::class, 'complete']);
            Route::patch('/assistances/{id}/cancel', [ProviderAssistanceController::class, 'cancel']);

            // Reseñas
            Route::get('/reviews', [ProviderReviewController::class, 'index']);
            Route::get('/reviews/{id}', [ProviderReviewController::class, 'show']);
        });

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRACIÓN
        |--------------------------------------------------------------------------
        | Recomendado después proteger con middleware role:admin
        |--------------------------------------------------------------------------
        */
        Route::prefix('admin')->group(function () {

            // Usuarios
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::post('/users', [AdminUserController::class, 'store']);
            Route::get('/users/{id}', [AdminUserController::class, 'show']);
            Route::put('/users/{id}', [AdminUserController::class, 'update']);
            Route::patch('/users/{id}', [AdminUserController::class, 'update']);
            Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
            Route::patch('/users/{id}/restore', [AdminUserController::class, 'restore']);
            Route::patch('/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus']);

            // Proveedores
            Route::get('/providers', [AdminProviderController::class, 'index']);
            Route::get('/providers/{id}', [AdminProviderController::class, 'show']);
            Route::patch('/providers/{id}/approve', [AdminProviderController::class, 'approve']);
            Route::patch('/providers/{id}/reject', [AdminProviderController::class, 'reject']);
            Route::patch('/providers/{id}/suspend', [AdminProviderController::class, 'suspend']);
            Route::patch('/providers/{id}/activate', [AdminProviderController::class, 'activate']);

            // Catálogo de servicios
            Route::get('/service-catalog', [AdminServiceCatalogController::class, 'index']);
            Route::post('/service-catalog', [AdminServiceCatalogController::class, 'store']);
            Route::get('/service-catalog/{id}', [AdminServiceCatalogController::class, 'show']);
            Route::put('/service-catalog/{id}', [AdminServiceCatalogController::class, 'update']);
            Route::patch('/service-catalog/{id}', [AdminServiceCatalogController::class, 'update']);
            Route::delete('/service-catalog/{id}', [AdminServiceCatalogController::class, 'destroy']);

            // Tipos de vehículo
            Route::get('/vehicle-types', [AdminVehicleTypeController::class, 'index']);
            Route::post('/vehicle-types', [AdminVehicleTypeController::class, 'store']);
            Route::get('/vehicle-types/{id}', [AdminVehicleTypeController::class, 'show']);
            Route::put('/vehicle-types/{id}', [AdminVehicleTypeController::class, 'update']);
            Route::patch('/vehicle-types/{id}', [AdminVehicleTypeController::class, 'update']);
            Route::delete('/vehicle-types/{id}', [AdminVehicleTypeController::class, 'destroy']);

            // Tipos de método de pago
            Route::get('/payment-method-types', [AdminPaymentMethodTypeController::class, 'index']);
            Route::post('/payment-method-types', [AdminPaymentMethodTypeController::class, 'store']);
            Route::get('/payment-method-types/{id}', [AdminPaymentMethodTypeController::class, 'show']);
            Route::put('/payment-method-types/{id}', [AdminPaymentMethodTypeController::class, 'update']);
            Route::patch('/payment-method-types/{id}', [AdminPaymentMethodTypeController::class, 'update']);
            Route::delete('/payment-method-types/{id}', [AdminPaymentMethodTypeController::class, 'destroy']);

            // Estados / catálogos de estatus
            Route::get('/statuses', [AdminStatusController::class, 'index']);
            Route::post('/statuses', [AdminStatusController::class, 'store']);
            Route::get('/statuses/{id}', [AdminStatusController::class, 'show']);
            Route::put('/statuses/{id}', [AdminStatusController::class, 'update']);
            Route::patch('/statuses/{id}', [AdminStatusController::class, 'update']);
            Route::delete('/statuses/{id}', [AdminStatusController::class, 'destroy']);

            // Área legal / documentos normativos
            Route::get('/legal', [AdminLegalController::class, 'index']);
            Route::post('/legal', [AdminLegalController::class, 'store']);
            Route::get('/legal/{id}', [AdminLegalController::class, 'show']);
            Route::put('/legal/{id}', [AdminLegalController::class, 'update']);
            Route::patch('/legal/{id}', [AdminLegalController::class, 'update']);
            Route::delete('/legal/{id}', [AdminLegalController::class, 'destroy']);

            // Supervisión de asistencias
            Route::get('/assistances', [AdminAssistanceController::class, 'index']);
            Route::get('/assistances/{id}', [AdminAssistanceController::class, 'show']);
            Route::patch('/assistances/{id}/assign', [AdminAssistanceController::class, 'assign']);
            Route::patch('/assistances/{id}/reassign', [AdminAssistanceController::class, 'reassign']);
            Route::patch('/assistances/{id}/cancel', [AdminAssistanceController::class, 'cancel']);
            Route::patch('/assistances/{id}/close', [AdminAssistanceController::class, 'close']);

            // Finanzas
            Route::get('/finances/overview', [AdminFinanceController::class, 'overview']);
            Route::get('/finances/payments', [AdminFinanceController::class, 'payments']);
            Route::get('/finances/payouts', [AdminFinanceController::class, 'payouts']);
            Route::get('/finances/reports', [AdminFinanceController::class, 'reports']);

            // Auditoría
            Route::get('/audit/logs', [AdminAuditController::class, 'logs']);
            Route::get('/audit/events', [AdminAuditController::class, 'events']);
            Route::get('/audit/users/{id}', [AdminAuditController::class, 'userActivity']);
        });
    });
});
