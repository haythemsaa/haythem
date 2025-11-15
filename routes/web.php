<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\FuelConsumptionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Gestion de Flotte - Véhicules
    Route::resource('vehicles', VehicleController::class);
    Route::get('/api/models-by-brand/{brand}', [VehicleController::class, 'getModelsByBrand'])
        ->name('api.models-by-brand');

    // Gestion RH - Employés
    Route::resource('employees', EmployeeController::class);

    // Maintenance GMAO - Interventions
    Route::resource('interventions', InterventionController::class);
    Route::post('/interventions/{intervention}/mark-urgent', [InterventionController::class, 'markAsUrgent'])
        ->name('interventions.mark-urgent');
    Route::post('/interventions/{intervention}/close', [InterventionController::class, 'close'])
        ->name('interventions.close');

    // Carburant - Consommations
    Route::resource('fuel-consumptions', FuelConsumptionController::class);
    Route::get('/fuel-analytics', [FuelConsumptionController::class, 'analytics'])
        ->name('fuel-consumptions.analytics');

    // TODO: Ajouter les routes pour les autres modules
    // Route::resource('documents', DocumentController::class);
    // etc...
});
