<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\Intervention;
use App\Models\TransportMission;
use App\Models\Site;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Statistiques des véhicules
        $totalVehicles = Vehicle::count();
        $availableVehicles = Vehicle::available()->count();
        $inMaintenanceVehicles = Vehicle::inMaintenance()->count();
        $inMissionVehicles = Vehicle::inMission()->count();

        // Statistiques des employés
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::active()->count();
        $drivers = Employee::drivers()->count();

        // Statistiques des interventions
        $totalInterventions = Intervention::count();
        $pendingInterventions = Intervention::pending()->count();
        $urgentInterventions = Intervention::urgent()->count();
        $inProgressInterventions = Intervention::inProgress()->count();

        // Statistiques missions transport
        $activeMissions = TransportMission::where('status', 'en_cours')->count();
        $plannedMissions = TransportMission::where('status', 'planifie')->count();

        // Sites
        $sites = Site::withCount('vehicles')->get();

        // Derniers véhicules ajoutés
        $recentVehicles = Vehicle::with(['brand', 'vehicleModel', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // Interventions urgentes récentes
        $recentUrgentInterventions = Intervention::with(['vehicle', 'requester', 'category'])
            ->urgent()
            ->pending()
            ->latest()
            ->take(5)
            ->get();

        // Répartition des véhicules par statut
        $vehiclesByStatus = [
            'disponible' => $availableVehicles,
            'en_mission' => $inMissionVehicles,
            'en_maintenance' => $inMaintenanceVehicles,
            'en_panne' => Vehicle::where('status', 'en_panne')->count(),
        ];

        return view('dashboard.index', compact(
            'totalVehicles',
            'availableVehicles',
            'inMaintenanceVehicles',
            'inMissionVehicles',
            'totalEmployees',
            'activeEmployees',
            'drivers',
            'totalInterventions',
            'pendingInterventions',
            'urgentInterventions',
            'inProgressInterventions',
            'activeMissions',
            'plannedMissions',
            'sites',
            'recentVehicles',
            'recentUrgentInterventions',
            'vehiclesByStatus'
        ));
    }
}
