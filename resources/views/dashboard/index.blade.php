@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0"><i class="fas fa-tachometer-alt"></i> Tableau de Bord</h1>
            <p class="text-muted">Vue d'ensemble de la gestion de flotte</p>
        </div>
    </div>

    <!-- KPI Cards - Véhicules -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Véhicules</h6>
                            <h2 class="mb-0">{{ $totalVehicles }}</h2>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-car fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Véhicules Disponibles</h6>
                            <h2 class="mb-0 text-success">{{ $availableVehicles }}</h2>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">En Maintenance</h6>
                            <h2 class="mb-0 text-warning">{{ $inMaintenanceVehicles }}</h2>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-tools fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Interventions Urgentes</h6>
                            <h2 class="mb-0 text-danger">{{ $urgentInterventions }}</h2>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards - RH et Opérations -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Employés</h6>
                            <h2 class="mb-0">{{ $totalEmployees }}</h2>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Employés Actifs</h6>
                            <h2 class="mb-0 text-success">{{ $activeEmployees }}</h2>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-user-check fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Interventions</h6>
                            <h2 class="mb-0">{{ $totalInterventions }}</h2>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-wrench fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Missions Transport</h6>
                            <h2 class="mb-0">{{ $totalMissions }}</h2>
                            <small class="text-muted">{{ $inProgressMissions }} en cours</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-truck fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row">
        <!-- Recent Vehicles -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-car"></i> Véhicules Récemment Ajoutés</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentVehicles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Immatriculation</th>
                                        <th>Véhicule</th>
                                        <th>Catégorie</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentVehicles as $vehicle)
                                        <tr>
                                            <td><strong>{{ $vehicle->registration_number }}</strong></td>
                                            <td>
                                                {{ $vehicle->brand?->name }} {{ $vehicle->vehicleModel?->name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $vehicle->category?->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($vehicle->status) {
                                                        'disponible' => 'success',
                                                        'en_mission' => 'primary',
                                                        'en_maintenance' => 'warning',
                                                        'hors_service' => 'danger',
                                                        'en_panne' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                    $statusLabel = match($vehicle->status) {
                                                        'disponible' => 'Disponible',
                                                        'en_mission' => 'En Mission',
                                                        'en_maintenance' => 'En Maintenance',
                                                        'hors_service' => 'Hors Service',
                                                        'en_panne' => 'En Panne',
                                                        default => $vehicle->status
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('vehicles.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-list"></i> Voir tous les véhicules
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-car fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun véhicule enregistré</p>
                            @can('create_vehicles')
                                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Ajouter un véhicule
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Urgent Interventions -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Interventions Urgentes</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentInterventions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Véhicule</th>
                                        <th>Titre</th>
                                        <th>Urgence</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentInterventions as $intervention)
                                        <tr>
                                            <td><strong>{{ $intervention->vehicle?->registration_number }}</strong></td>
                                            <td>{{ Str::limit($intervention->title, 30) }}</td>
                                            <td>
                                                @php
                                                    $urgencyClass = match($intervention->urgency) {
                                                        'tres_urgent' => 'danger',
                                                        'urgent' => 'warning',
                                                        'normal' => 'info',
                                                        'faible' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                    $urgencyLabel = match($intervention->urgency) {
                                                        'tres_urgent' => 'Très Urgent',
                                                        'urgent' => 'Urgent',
                                                        'normal' => 'Normal',
                                                        'faible' => 'Faible',
                                                        default => $intervention->urgency
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $urgencyClass }}">{{ $urgencyLabel }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($intervention->status) {
                                                        'en_attente' => 'warning',
                                                        'diagnostique' => 'info',
                                                        'en_reparation' => 'primary',
                                                        'cloture' => 'success',
                                                        'annule' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                    $statusLabel = match($intervention->status) {
                                                        'en_attente' => 'En Attente',
                                                        'diagnostique' => 'Diagnostic',
                                                        'en_reparation' => 'En Réparation',
                                                        'cloture' => 'Clôturé',
                                                        'annule' => 'Annulé',
                                                        default => $intervention->status
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <a href="#" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-list"></i> Voir toutes les interventions
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">Aucune intervention urgente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section (Placeholder for future implementation) -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Répartition par Statut</h5>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">Graphique à venir (Chart.js)</p>
                    <div style="height: 250px; background-color: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-pie fa-4x text-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Évolution Mensuelle</h5>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">Graphique à venir (Chart.js)</p>
                    <div style="height: 250px; background-color: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line fa-4x text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
