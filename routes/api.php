<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\RentalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
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
    
});

// Public endpoints (no auth required)
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'version' => '1.0.0'
    ]);
});
