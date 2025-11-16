<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\InsuranceController;
use App\Http\Controllers\Api\FuelConsumptionController;
use App\Http\Controllers\Api\InterventionController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // Dashboard API
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/alerts', [DashboardController::class, 'alerts']);
    Route::get('dashboard/performance', [DashboardController::class, 'performance']);
    Route::get('dashboard/trends', [DashboardController::class, 'trends']);

    // Vehicles API
    Route::apiResource('vehicles', VehicleController::class);
    Route::get('vehicles-available', [VehicleController::class, 'available']);
    Route::get('vehicles-maintenance', [VehicleController::class, 'maintenance']);
    Route::get('vehicles-stats', [VehicleController::class, 'stats']);

    // Rentals API
    Route::apiResource('rentals', RentalController::class);
    Route::get('rentals-ongoing', [RentalController::class, 'ongoing']);
    Route::post('rentals/{rental}/complete', [RentalController::class, 'complete']);
    Route::get('rentals-stats', [RentalController::class, 'stats']);

    // Insurance API
    Route::apiResource('insurances', InsuranceController::class);
    Route::get('insurances-expiring', [InsuranceController::class, 'expiring']);
    Route::get('insurances-expired', [InsuranceController::class, 'expired']);
    Route::get('insurances-stats', [InsuranceController::class, 'stats']);

    // Fuel Consumption API
    Route::apiResource('fuel-consumptions', FuelConsumptionController::class);
    Route::get('fuel-consumptions-stats', [FuelConsumptionController::class, 'stats']);
    Route::get('fuel-consumptions-trend', [FuelConsumptionController::class, 'trend']);

    // Interventions API
    Route::apiResource('interventions', InterventionController::class);
    Route::post('interventions/{intervention}/close', [InterventionController::class, 'close']);
    Route::get('interventions-pending', [InterventionController::class, 'pending']);
    Route::get('interventions-urgent', [InterventionController::class, 'urgent']);
    Route::get('interventions-stats', [InterventionController::class, 'stats']);

    // Mobile-Optimized Endpoints
    Route::prefix('mobile')->name('mobile.')->group(function () {
        // Mobile Dashboard
        Route::get('dashboard', [\App\Http\Controllers\Api\MobileController::class, 'dashboard']);

        // Quick Actions
        Route::get('quick-stats', [\App\Http\Controllers\Api\MobileController::class, 'quickStats']);

        // GPS Tracking
        Route::post('gps/location', [\App\Http\Controllers\Api\MobileController::class, 'updateLocation']);
        Route::get('gps/nearby-vehicles', [\App\Http\Controllers\Api\MobileController::class, 'nearbyVehicles']);

        // Notifications
        Route::get('notifications', [\App\Http\Controllers\Api\MobileController::class, 'notifications']);
        Route::post('notifications/{id}/read', [\App\Http\Controllers\Api\MobileController::class, 'markNotificationRead']);

        // Quick Vehicle Info
        Route::get('vehicles/search', [\App\Http\Controllers\Api\MobileController::class, 'searchVehicles']);
        Route::get('vehicles/{vehicle}/quick-info', [\App\Http\Controllers\Api\MobileController::class, 'vehicleQuickInfo']);

        // Quick Fuel Entry
        Route::post('fuel/quick-entry', [\App\Http\Controllers\Api\MobileController::class, 'quickFuelEntry']);

        // Quick Intervention Report
        Route::post('interventions/quick-report', [\App\Http\Controllers\Api\MobileController::class, 'quickInterventionReport']);
    });

});

// Public endpoints (no auth required)
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});
