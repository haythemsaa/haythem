@extends('layouts.app')

@section('title', 'Opérations en Lot')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-tasks"></i> Opérations en Lot sur Véhicules</h1>
            <p class="text-muted">Effectuez des modifications sur plusieurs véhicules simultanément</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Vehicle Selection -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-check-square"></i> Sélection des Véhicules</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                    <i class="fas fa-check-double"></i> Tout Sélectionner
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                    <i class="fas fa-times"></i> Tout Désélectionner
                </button>
                <span class="ms-3 badge bg-info" id="selectedCount">0 sélectionné(s)</span>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover" id="vehiclesTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                            </th>
                            <th>Immatriculation</th>
                            <th>Marque/Modèle</th>
                            <th>Année</th>
                            <th>Statut</th>
                            <th>Site</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicle)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input vehicle-checkbox" value="{{ $vehicle->id }}">
                                </td>
                                <td><strong>{{ $vehicle->registration_number }}</strong></td>
                                <td>{{ $vehicle->brand->name ?? '' }} {{ $vehicle->vehicleModel->name ?? '' }}</td>
                                <td>{{ $vehicle->year }}</td>
                                <td>
                                    @php
                                        $statusClass = match($vehicle->status) {
                                            'disponible' => 'success',
                                            'en_mission' => 'primary',
                                            'en_maintenance' => 'warning',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ $vehicle->status }}</span>
                                </td>
                                <td>{{ $vehicle->site->name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Batch Operations -->
    <div class="row">
        <!-- Update Status -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-sync"></i> Mise à Jour du Statut</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vehicles.batch.status') }}" id="statusForm">
                        @csrf
                        <input type="hidden" name="vehicle_ids" id="statusVehicleIds">

                        <div class="mb-3">
                            <label for="status" class="form-label">Nouveau Statut</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="disponible">✅ Disponible</option>
                                <option value="en_mission">🚗 En Mission</option>
                                <option value="en_maintenance">🔧 En Maintenance</option>
                                <option value="en_panne">⛔ En Panne</option>
                                <option value="hors_service">🚫 Hors Service</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" disabled id="statusBtn">
                            <i class="fas fa-sync"></i> Mettre à Jour le Statut
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assign Site -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Assigner un Site</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vehicles.batch.site') }}" id="siteForm">
                        @csrf
                        <input type="hidden" name="vehicle_ids" id="siteVehicleIds">

                        <div class="mb-3">
                            <label for="site_id" class="form-label">Site/Dépôt</label>
                            <select name="site_id" id="site_id" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach(\App\Models\Site::all() as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }} - {{ $site->city }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" disabled id="siteBtn">
                            <i class="fas fa-map-marker-alt"></i> Assigner au Site
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Export -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-file-export"></i> Exporter la Sélection</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vehicles.batch.export') }}" id="exportForm">
                        @csrf
                        <input type="hidden" name="vehicle_ids" id="exportVehicleIds">

                        <div class="mb-3">
                            <label for="format" class="form-label">Format d'Export</label>
                            <select name="format" id="format" class="form-select" required>
                                <option value="pdf">📄 PDF</option>
                                <option value="excel">📊 Excel</option>
                                <option value="csv">📋 CSV</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100" disabled id="exportBtn">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-trash"></i> Suppression en Lot</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vehicles.batch.delete') }}" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="vehicle_ids" id="deleteVehicleIds">

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Attention:</strong> Cette action est irréversible!
                        </div>

                        <button type="submit" class="btn btn-danger w-100" disabled id="deleteBtn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ces véhicules ?')">
                            <i class="fas fa-trash"></i> Supprimer la Sélection
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Custom Field Update -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Mise à Jour de Champ Personnalisé</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vehicles.batch.field') }}" id="fieldForm" class="row g-3">
                        @csrf
                        <input type="hidden" name="vehicle_ids" id="fieldVehicleIds">

                        <div class="col-md-4">
                            <label for="field" class="form-label">Champ à Modifier</label>
                            <select name="field" id="field" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="color">Couleur</option>
                                <option value="year">Année</option>
                                <option value="fuel_tank_capacity">Capacité Réservoir (L)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="value" class="form-label">Nouvelle Valeur</label>
                            <input type="text" name="value" id="value" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100" disabled id="fieldBtn">
                                <i class="fas fa-save"></i> Mettre à Jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectedCount = document.getElementById('selectedCount');

    const statusBtn = document.getElementById('statusBtn');
    const siteBtn = document.getElementById('siteBtn');
    const exportBtn = document.getElementById('exportBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const fieldBtn = document.getElementById('fieldBtn');

    // Update selected count and button states
    function updateSelection() {
        const selected = Array.from(checkboxes).filter(cb => cb.checked);
        selectedCount.textContent = `${selected.length} sélectionné(s)`;

        const hasSelection = selected.length > 0;
        statusBtn.disabled = !hasSelection;
        siteBtn.disabled = !hasSelection;
        exportBtn.disabled = !hasSelection;
        deleteBtn.disabled = !hasSelection;
        fieldBtn.disabled = !hasSelection;

        // Update hidden inputs
        const selectedIds = selected.map(cb => cb.value);
        document.getElementById('statusVehicleIds').value = JSON.stringify(selectedIds);
        document.getElementById('siteVehicleIds').value = JSON.stringify(selectedIds);
        document.getElementById('exportVehicleIds').value = JSON.stringify(selectedIds);
        document.getElementById('deleteVehicleIds').value = JSON.stringify(selectedIds);
        document.getElementById('fieldVehicleIds').value = JSON.stringify(selectedIds);
    }

    // Select all
    selectAllCheckbox.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelection();
    });

    document.getElementById('selectAll').addEventListener('click', function() {
        checkboxes.forEach(cb => cb.checked = true);
        selectAllCheckbox.checked = true;
        updateSelection();
    });

    document.getElementById('deselectAll').addEventListener('click', function() {
        checkboxes.forEach(cb => cb.checked = false);
        selectAllCheckbox.checked = false;
        updateSelection();
    });

    // Individual checkbox change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelection);
    });

    // Initial update
    updateSelection();
});
</script>
@endpush
@endsection
