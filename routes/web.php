<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\EmployeeController;

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

    // TODO: Ajouter les routes pour les autres modules
    // Route::resource('interventions', InterventionController::class);
    // Route::resource('fuel-consumptions', FuelConsumptionController::class);
    // etc...
});
