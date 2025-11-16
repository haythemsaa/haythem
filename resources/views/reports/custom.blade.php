@extends('layouts.app')

@section('title', 'Générateur de Rapports Personnalisés')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-magic"></i> Générateur de Rapports Personnalisés</h1>
            <p class="text-muted">Créez des rapports sur mesure avec des filtres avancés</p>
        </div>
    </div>

    <form method="POST" action="{{ route('reports.generate') }}" id="customReportForm">
        @csrf

        <div class="row">
            <!-- Left Column - Configuration -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Configuration du Rapport</h5>
                    </div>
                    <div class="card-body">
                        <!-- Report Type -->
                        <div class="mb-4">
                            <label class="form-label"><strong>Type de Rapport</strong></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="report_type" id="type_fleet" value="fleet" checked>
                                        <label class="form-check-label" for="type_fleet">
                                            <i class="fas fa-car"></i> <strong>Rapport Flotte</strong><br>
                                            <small class="text-muted">Vue d'ensemble des véhicules</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="report_type" id="type_maintenance" value="maintenance">
                                        <label class="form-check-label" for="type_maintenance">
                                            <i class="fas fa-tools"></i> <strong>Rapport Maintenance</strong><br>
                                            <small class="text-muted">Interventions et coûts</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="report_type" id="type_fuel" value="fuel">
                                        <label class="form-check-label" for="type_fuel">
                                            <i class="fas fa-gas-pump"></i> <strong>Rapport Carburant</strong><br>
                                            <small class="text-muted">Consommation et coûts</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="report_type" id="type_financial" value="financial">
                                        <label class="form-check-label" for="type_financial">
                                            <i class="fas fa-chart-line"></i> <strong>Rapport Financier</strong><br>
                                            <small class="text-muted">Analyse des coûts globaux</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Date Range -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label"><strong>Date Début</strong></label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label"><strong>Date Fin</strong></label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <hr>

                        <!-- Filters Section -->
                        <div id="filtersSection">
                            <h6><i class="fas fa-filter"></i> Filtres Avancés</h6>

                            <!-- Fleet Filters -->
                            <div id="fleet_filters" class="filters-group">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="filter_status" class="form-label">Statut</label>
                                        <select class="form-select" id="filter_status" name="filters[status]">
                                            <option value="">Tous</option>
                                            <option value="disponible">Disponible</option>
                                            <option value="en_mission">En Mission</option>
                                            <option value="en_maintenance">En Maintenance</option>
                                            <option value="hors_service">Hors Service</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="filter_site" class="form-label">Site</label>
                                        <select class="form-select" id="filter_site" name="filters[site_id]">
                                            <option value="">Tous</option>
                                            @foreach(\App\Models\Site::all() as $site)
                                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Maintenance Filters -->
                            <div id="maintenance_filters" class="filters-group d-none">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="filter_type" class="form-label">Type</label>
                                        <select class="form-select" id="filter_type" name="filters[type]">
                                            <option value="">Tous</option>
                                            <option value="preventive">Préventive</option>
                                            <option value="curative">Curative</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="filter_urgency" class="form-label">Urgence</label>
                                        <select class="form-select" id="filter_urgency" name="filters[urgency]">
                                            <option value="">Tous</option>
                                            <option value="tres_urgent">Très Urgent</option>
                                            <option value="urgent">Urgent</option>
                                            <option value="normal">Normal</option>
                                            <option value="faible">Faible</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Fuel Filters -->
                            <div id="fuel_filters" class="filters-group d-none">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="filter_vehicle" class="form-label">Véhicule</label>
                                        <select class="form-select" id="filter_vehicle" name="filters[vehicle_id]">
                                            <option value="">Tous</option>
                                            @foreach(\App\Models\Vehicle::orderBy('registration_number')->get() as $vehicle)
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Export Options -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-file-export"></i> Options d'Export</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Format</strong></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_pdf" value="pdf" checked>
                                <label class="form-check-label" for="format_pdf">
                                    <i class="fas fa-file-pdf text-danger"></i> PDF
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_excel" value="excel">
                                <label class="form-check-label" for="format_excel">
                                    <i class="fas fa-file-excel text-success"></i> Excel
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_csv" value="csv">
                                <label class="form-check-label" for="format_csv">
                                    <i class="fas fa-file-csv text-info"></i> CSV
                                </label>
                            </div>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-download"></i> Générer le Rapport
                        </button>
                    </div>
                </div>

                <!-- Quick Presets -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Préréglages Rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setPreset('this_month')">
                                <i class="fas fa-calendar"></i> Ce Mois
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setPreset('last_month')">
                                <i class="fas fa-calendar-minus"></i> Mois Dernier
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setPreset('this_quarter')">
                                <i class="fas fa-calendar-alt"></i> Ce Trimestre
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setPreset('this_year')">
                                <i class="fas fa-calendar-check"></i> Cette Année
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle report type change
    const reportTypeRadios = document.querySelectorAll('input[name="report_type"]');
    reportTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Hide all filter groups
            document.querySelectorAll('.filters-group').forEach(group => {
                group.classList.add('d-none');
            });

            // Show relevant filter group
            const filterGroup = document.getElementById(this.value + '_filters');
            if (filterGroup) {
                filterGroup.classList.remove('d-none');
            }
        });
    });

    // Date presets
    window.setPreset = function(preset) {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const now = new Date();

        switch(preset) {
            case 'this_month':
                startDate.value = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
                endDate.value = now.toISOString().split('T')[0];
                break;
            case 'last_month':
                startDate.value = new Date(now.getFullYear(), now.getMonth() - 1, 1).toISOString().split('T')[0];
                endDate.value = new Date(now.getFullYear(), now.getMonth(), 0).toISOString().split('T')[0];
                break;
            case 'this_quarter':
                const quarter = Math.floor(now.getMonth() / 3);
                startDate.value = new Date(now.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
                endDate.value = now.toISOString().split('T')[0];
                break;
            case 'this_year':
                startDate.value = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
                endDate.value = now.toISOString().split('T')[0];
                break;
        }
    };
});
</script>
@endpush
@endsection
