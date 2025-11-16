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

    <!-- Charts Section -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Répartition par Statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Tendance Carburant (12 mois)</h5>
                </div>
                <div class="card-body">
                    <canvas id="fuelTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Coûts Maintenance (12 mois)</h5>
                </div>
                <div class="card-body">
                    <canvas id="maintenanceTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-area"></i> Revenus Location (12 mois)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Fleet Alerts -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-bell"></i> Alertes Flotte</h5>
                </div>
                <div class="card-body">
                    <div id="alertsContainer">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vehicle Status Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Disponible', 'En Mission', 'En Maintenance', 'Hors Service'],
            datasets: [{
                data: [{{ $availableVehicles }}, {{ $totalVehicles - $availableVehicles - $inMaintenanceVehicles }}, {{ $inMaintenanceVehicles }}, 0],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(0, 123, 255, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Fetch and display Fuel Trend
    fetch('/api/dashboard/trends?type=fuel&months=12', {
        headers: {
            'Authorization': 'Bearer {{ auth()->user()?->createToken("dashboard")->plainTextToken ?? "" }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            const fuelCtx = document.getElementById('fuelTrendChart').getContext('2d');
            new Chart(fuelCtx, {
                type: 'line',
                data: {
                    labels: data.data.map(item => item.month),
                    datasets: [{
                        label: 'Coût Carburant (DH)',
                        data: data.data.map(item => item.total_cost),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' DH';
                                }
                            }
                        }
                    }
                }
            });
        }
    })
    .catch(error => console.error('Fuel trend error:', error));

    // Fetch and display Maintenance Trend
    fetch('/api/dashboard/trends?type=maintenance&months=12', {
        headers: {
            'Authorization': 'Bearer {{ auth()->user()?->createToken("dashboard")->plainTextToken ?? "" }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            const maintenanceCtx = document.getElementById('maintenanceTrendChart').getContext('2d');
            new Chart(maintenanceCtx, {
                type: 'bar',
                data: {
                    labels: data.data.map(item => item.month),
                    datasets: [{
                        label: 'Coût Maintenance (DH)',
                        data: data.data.map(item => item.total_cost),
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' DH';
                                }
                            }
                        }
                    }
                }
            });
        }
    })
    .catch(error => console.error('Maintenance trend error:', error));

    // Fetch and display Revenue Trend
    fetch('/api/dashboard/trends?type=revenue&months=12', {
        headers: {
            'Authorization': 'Bearer {{ auth()->user()?->createToken("dashboard")->plainTextToken ?? "" }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            const revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: data.data.map(item => item.month),
                    datasets: [{
                        label: 'Revenus Location (DH)',
                        data: data.data.map(item => item.total_revenue),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' DH';
                                }
                            }
                        }
                    }
                }
            });
        }
    })
    .catch(error => console.error('Revenue trend error:', error));

    // Fetch and display alerts
    fetch('/api/dashboard/alerts', {
        headers: {
            'Authorization': 'Bearer {{ auth()->user()?->createToken("dashboard")->plainTextToken ?? "" }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const alertsContainer = document.getElementById('alertsContainer');
            let alertsHtml = '';

            // Critical Alerts
            if (data.data.critical && data.data.critical.length > 0) {
                data.data.critical.forEach(alert => {
                    alertsHtml += `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <strong>${alert.title || 'Alerte Critique'}</strong><br>
                            ${alert.message}
                            ${alert.action_url ? `<a href="${alert.action_url}" class="alert-link ms-2">Voir détails</a>` : ''}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                });
            }

            // Warning Alerts
            if (data.data.warnings && data.data.warnings.length > 0) {
                data.data.warnings.forEach(alert => {
                    alertsHtml += `
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <strong>${alert.title || 'Avertissement'}</strong><br>
                            ${alert.message}
                            ${alert.action_url ? `<a href="${alert.action_url}" class="alert-link ms-2">Voir détails</a>` : ''}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                });
            }

            // Info Alerts
            if (data.data.info && data.data.info.length > 0) {
                data.data.info.forEach(alert => {
                    alertsHtml += `
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i> <strong>${alert.title || 'Information'}</strong><br>
                            ${alert.message}
                            ${alert.action_url ? `<a href="${alert.action_url}" class="alert-link ms-2">Voir détails</a>` : ''}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                });
            }

            if (alertsHtml === '') {
                alertsHtml = `
                    <div class="alert alert-success mb-0" role="alert">
                        <i class="fas fa-check-circle"></i> Aucune alerte. Tout va bien !
                    </div>
                `;
            }

            alertsContainer.innerHTML = alertsHtml;
        }
    })
    .catch(error => {
        console.error('Alerts error:', error);
        document.getElementById('alertsContainer').innerHTML = `
            <div class="alert alert-warning mb-0" role="alert">
                <i class="fas fa-exclamation-triangle"></i> Impossible de charger les alertes
            </div>
        `;
    });
});
</script>
@endpush
