<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\FuelConsumptionController;
use App\Http\Controllers\VehicleDocumentController;
use App\Http\Controllers\AccidentController;
use App\Http\Controllers\TrafficViolationController;
use App\Http\Controllers\DrivingLicenseController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TireController;
use App\Http\Controllers\MedicalCheckupController;
use App\Http\Controllers\PersonalProtectiveEquipmentController;

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

    // Accidents et Sinistres
    Route::resource('accidents', AccidentController::class);
    Route::post('/accidents/{accident}/close', [AccidentController::class, 'close'])
        ->name('accidents.close');
    Route::post('/accidents/{accident}/upload-document', [AccidentController::class, 'uploadDocument'])
        ->name('accidents.upload-document');
    Route::delete('/accident-documents/{accidentDocument}', [AccidentController::class, 'deleteDocument'])
        ->name('accident-documents.destroy');

    // Infractions Routières
    Route::resource('traffic-violations', TrafficViolationController::class);

    // Permis de Conduire
    Route::resource('driving-licenses', DrivingLicenseController::class);
    Route::get('/driving-licenses-alerts', [DrivingLicenseController::class, 'alerts'])
        ->name('driving-licenses.alerts');
    Route::post('/driving-licenses/{drivingLicense}/update-points', [DrivingLicenseController::class, 'updatePoints'])
        ->name('driving-licenses.update-points');

    // Certifications et Habilitations
    Route::resource('certifications', CertificationController::class);
    Route::get('/certifications-alerts', [CertificationController::class, 'alerts'])
        ->name('certifications.alerts');

    // Formations
    Route::resource('trainings', TrainingController::class);

    // Pneumatiques
    Route::resource('tires', TireController::class);
    Route::get('/tires-alerts', [TireController::class, 'alerts'])
        ->name('tires.alerts');
    Route::get('/tires/{tire}/rotate', [TireController::class, 'rotate'])
        ->name('tires.rotate');
    Route::post('/tires/{tire}/rotate', [TireController::class, 'storeRotation'])
        ->name('tires.store-rotation');

    // Visites Médicales
    Route::resource('medical-checkups', MedicalCheckupController::class);
    Route::get('/medical-checkups-alerts', [MedicalCheckupController::class, 'alerts'])
        ->name('medical-checkups.alerts');
    Route::get('/medical-checkups/{medicalCheckup}/download', [MedicalCheckupController::class, 'download'])
        ->name('medical-checkups.download');

    // EPI - Équipements de Protection Individuelle
    Route::resource('ppe', PersonalProtectiveEquipmentController::class);
    Route::get('/ppe-alerts', [PersonalProtectiveEquipmentController::class, 'alerts'])
        ->name('ppe.alerts');

    // TODO: Ajouter les routes pour les autres modules
    // etc...
});
