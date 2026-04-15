<?php

use App\Http\Controllers\Api\V1\Admin\AdminAssistanceController;
use App\Http\Controllers\Api\V1\Admin\AdminAuditController;
use App\Http\Controllers\Api\V1\Admin\AdminFinanceController;
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
use App\Http\Controllers\Api\V1\Client\ClientVehicleController;
use App\Http\Controllers\Api\V1\Common\NotificationController;
use App\Http\Controllers\Api\V1\Common\PaymentMethodTypeController;
use App\Http\Controllers\Api\V1\Common\ProfileController;
use App\Http\Controllers\Api\V1\Common\ServiceController;
use App\Http\Controllers\Api\V1\Provider\ProviderAssistanceController;
use App\Http\Controllers\Api\V1\Provider\ProviderDocumentController;
use App\Http\Controllers\Api\V1\Provider\ProviderProfileController;
use App\Http\Controllers\Api\V1\Provider\ProviderScheduleController;
use App\Http\Controllers\Api\V1\Provider\ProviderServiceController;
use App\Http\Controllers\Api\V1\Provider\ProviderTrackingController;
use App\Http\Controllers\Api\V1\Client\ClientTrackingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
    });

    Route::get('/services', [ServiceController::class, 'index']);

    Route::prefix('payment-method-types')->controller(PaymentMethodTypeController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->controller(AuthController::class)->group(function () {
            Route::get('/me', 'me');
            Route::post('/logout', 'logout');
            Route::post('/logout-all', 'logoutAll');
        });

        Route::prefix('me')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'update');
            Route::patch('/', 'update');
        });

        Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::patch('/{id}/read', 'markAsRead');
            Route::patch('/read-all', 'markAllAsRead');
        });

        Route::prefix('client')->middleware('role:client')->group(function () {
            Route::prefix('vehicles')->controller(ClientVehicleController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('addresses')->controller(ClientAddressController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('payment-methods')->controller(ClientPaymentMethodController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('assistance-requests')->controller(ClientAssistanceRequestController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::patch('/{id}/cancel', 'cancel');
                Route::get('/{id}/status', 'status');
                Route::get('/{id}/timeline', 'timeline');
            });

            Route::get('/assistance-requests/{id}/tracking', [ClientTrackingController::class, 'show']);

            Route::prefix('payments')->controller(ClientPaymentController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::get('/{id}/receipt', 'receipt');
            });
        });

        Route::prefix('provider')->middleware('role:provider')->group(function () {
            Route::prefix('profile')->controller(ProviderProfileController::class)->group(function () {
                Route::post('/', 'store');
                Route::get('/', 'show');
                Route::put('/', 'update');
                Route::patch('/', 'update');
                Route::delete('/', 'destroy');
            });

            Route::prefix('services')->controller(ProviderServiceController::class)->group(function () {
                Route::get('/', 'index');
                Route::put('/', 'update');
                Route::patch('/', 'update');
            });

            Route::prefix('schedules')->controller(ProviderScheduleController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('documents')->controller(ProviderDocumentController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('tracking')->controller(ProviderTrackingController::class)->group(function () {
                Route::post('/', 'store');
            });

            Route::prefix('assistance-requests')->controller(ProviderAssistanceController::class)->group(function () {
                Route::get('/available', 'available');
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::patch('/{id}/accept', 'accept');
                Route::patch('/{id}/status', 'updateStatus');
            });
        });

        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::prefix('providers')->controller(AdminProviderController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
            });

            Route::prefix('users')->controller(AdminUserController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
            });

            Route::prefix('services')->controller(AdminServiceCatalogController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('vehicle-types')->controller(AdminVehicleTypeController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('statuses')->controller(AdminStatusController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('payment-method-types')->controller(AdminPaymentMethodTypeController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::prefix('audit-logs')->controller(AdminAuditController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
            });

            Route::prefix('assistance-requests')->controller(AdminAssistanceController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::patch('/{id}', 'update');
            });

            Route::prefix('finance')->group(function () {
                Route::prefix('payments')->controller(AdminFinanceController::class)->group(function () {
                    Route::get('/', 'payments');
                    Route::get('/{id}', 'showPayment');
                    Route::put('/{id}', 'updatePayment');
                    Route::patch('/{id}', 'updatePayment');
                });

                Route::prefix('transactions')->controller(AdminFinanceController::class)->group(function () {
                    Route::get('/', 'transactions');
                    Route::get('/{id}', 'showTransaction');
                });
            });
        });
    });
});
