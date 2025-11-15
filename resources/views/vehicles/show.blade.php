@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-car"></i>
                {{ $vehicle->brand?->name }} {{ $vehicle->vehicleModel?->name }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Véhicules</a></li>
                    <li class="breadcrumb-item active">{{ $vehicle->registration_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            @can('edit_vehicles')
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            @endcan
            @can('delete_vehicles')
                <form action="{{ route('vehicles.destroy', $vehicle) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Vehicle Status Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h4 class="mb-0">{{ $vehicle->registration_number }}</h4>
                            <p class="text-muted mb-0">Immatriculation</p>
                        </div>
                        <div class="col-md-2">
                            <h5 class="mb-0">{{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km</h5>
                            <p class="text-muted mb-0">Kilométrage</p>
                        </div>
                        <div class="col-md-2">
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
                            <h5 class="mb-0"><span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span></h5>
                            <p class="text-muted mb-0">Statut</p>
                        </div>
                        <div class="col-md-2">
                            <h5 class="mb-0">{{ $vehicle->site?->name ?? 'N/A' }}</h5>
                            <p class="text-muted mb-0">Site</p>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0">{{ $vehicle->parc?->name ?? 'N/A' }}</h5>
                            <p class="text-muted mb-0">Parc</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-6">
            <!-- General Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Immatriculation</th>
                                <td><strong>{{ $vehicle->registration_number }}</strong></td>
                            </tr>
                            <tr>
                                <th>Code Interne</th>
                                <td>{{ $vehicle->internal_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Numéro de Flotte</th>
                                <td>{{ $vehicle->fleet_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Numéro de Châssis (VIN)</th>
                                <td><code>{{ $vehicle->vin }}</code></td>
                            </tr>
                            <tr>
                                <th>Marque</th>
                                <td>{{ $vehicle->brand?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Modèle</th>
                                <td>{{ $vehicle->vehicleModel?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Catégorie</th>
                                <td>
                                    <span class="badge bg-secondary">{{ $vehicle->category?->name ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Technical Specifications -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Caractéristiques Techniques</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Année</th>
                                <td>{{ $vehicle->year ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Type d'Énergie</th>
                                <td>{{ $vehicle->energy_type ? ucfirst($vehicle->energy_type) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Type de Carburant</th>
                                <td>{{ $vehicle->fuelType?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Capacité Réservoir</th>
                                <td>{{ $vehicle->tank_capacity ? $vehicle->tank_capacity . ' L' : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Puissance Moteur</th>
                                <td>{{ $vehicle->engine_power ? $vehicle->engine_power . ' CV' : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Nombre de Places</th>
                                <td>{{ $vehicle->seats ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Nombre de Portes</th>
                                <td>{{ $vehicle->doors ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Couleur</th>
                                <td>{{ $vehicle->color ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <!-- Acquisition Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Informations d'Acquisition</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Mode d'Acquisition</th>
                                <td>{{ $vehicle->acquisitionMode?->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Date d'Achat</th>
                                <td>{{ $vehicle->purchase_date?->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Prix d'Achat</th>
                                <td>
                                    @if($vehicle->purchase_price)
                                        <strong>{{ number_format($vehicle->purchase_price, 2, ',', ' ') }} €</strong>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Fournisseur</th>
                                <td>{{ $vehicle->supplier?->name ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mileage and Affectation -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Kilométrage et Affectation</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Kilométrage Actuel</th>
                                <td><strong>{{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km</strong></td>
                            </tr>
                            <tr>
                                <th>Kilométrage Acquisition</th>
                                <td>{{ number_format($vehicle->mileage_acquisition ?? 0, 0, ',', ' ') }} km</td>
                            </tr>
                            <tr>
                                <th>Kilométrage Parcouru</th>
                                <td>
                                    {{ number_format(($vehicle->current_mileage ?? 0) - ($vehicle->mileage_acquisition ?? 0), 0, ',', ' ') }} km
                                </td>
                            </tr>
                            <tr>
                                <th>Site</th>
                                <td>{{ $vehicle->site?->name ?? 'Non affecté' }}</td>
                            </tr>
                            <tr>
                                <th>Parc</th>
                                <td>{{ $vehicle->parc?->name ?? 'Non affecté' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Historique</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Créé le</th>
                                <td>{{ $vehicle->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Dernière modification</th>
                                <td>{{ $vehicle->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Related Data -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="interventions-tab" data-bs-toggle="tab"
                                    data-bs-target="#interventions" type="button" role="tab">
                                <i class="fas fa-wrench"></i> Interventions
                                <span class="badge bg-secondary">{{ $vehicle->interventions_count ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab"
                                    data-bs-target="#documents" type="button" role="tab">
                                <i class="fas fa-file-alt"></i> Documents
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fuel-tab" data-bs-toggle="tab"
                                    data-bs-target="#fuel" type="button" role="tab">
                                <i class="fas fa-gas-pump"></i> Carburant
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="missions-tab" data-bs-toggle="tab"
                                    data-bs-target="#missions" type="button" role="tab">
                                <i class="fas fa-route"></i> Missions
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="interventions" role="tabpanel">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Les interventions de maintenance seront affichées ici une fois le module développé.
                            </p>
                        </div>
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Les documents du véhicule (assurance, carte grise, etc.) seront affichés ici.
                            </p>
                        </div>
                        <div class="tab-pane fade" id="fuel" role="tabpanel">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                L'historique de consommation de carburant sera affiché ici.
                            </p>
                        </div>
                        <div class="tab-pane fade" id="missions" role="tabpanel">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Les missions de transport associées à ce véhicule seront affichées ici.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
