<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\FuelConsumptionController;
use App\Http\Controllers\VehicleDocumentController;

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

    // Documents Administratifs - Véhicules
    Route::resource('vehicle-documents', VehicleDocumentController::class);
    Route::get('/documents-alerts', [VehicleDocumentController::class, 'alerts'])
        ->name('vehicle-documents.alerts');
    Route::get('/vehicle-documents/{vehicleDocument}/download', [VehicleDocumentController::class, 'download'])
        ->name('vehicle-documents.download');

    // TODO: Ajouter les routes pour les autres modules
    // Route::resource('accidents', AccidentController::class);
    // etc...
});
