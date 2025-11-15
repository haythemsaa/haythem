@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0"><i class="fas fa-car"></i> Gestion des Véhicules</h1>
            <p class="text-muted">Liste et gestion de la flotte automobile</p>
        </div>
        <div class="col-md-6 text-end">
            @can('create_vehicles')
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Véhicule
                </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('vehicles.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}"
                               placeholder="Immatriculation, code...">
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="disponible" {{ request('status') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="en_mission" {{ request('status') == 'en_mission' ? 'selected' : '' }}>En Mission</option>
                            <option value="en_maintenance" {{ request('status') == 'en_maintenance' ? 'selected' : '' }}>En Maintenance</option>
                            <option value="en_panne" {{ request('status') == 'en_panne' ? 'selected' : '' }}>En Panne</option>
                            <option value="hors_service" {{ request('status') == 'hors_service' ? 'selected' : '' }}>Hors Service</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="site_id" class="form-label">Site</label>
                        <select class="form-select" id="site_id" name="site_id">
                            <option value="">Tous les sites</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="category_id" class="form-label">Catégorie</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Toutes catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Liste des Véhicules
                <span class="badge bg-light text-dark">{{ $vehicles->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($vehicles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Immatriculation</th>
                                <th>Code Interne</th>
                                <th>Véhicule</th>
                                <th>Catégorie</th>
                                <th>Site</th>
                                <th>Kilométrage</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                                <tr>
                                    <td><strong>{{ $vehicle->registration_number }}</strong></td>
                                    <td>{{ $vehicle->internal_code }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $vehicle->brand?->name }} {{ $vehicle->vehicleModel?->name }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $vehicle->fleet_number ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $vehicle->category?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $vehicle->site?->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km</td>
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
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('view_vehicles')
                                                <a href="{{ route('vehicles.show', $vehicle) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('edit_vehicles')
                                                <a href="{{ route('vehicles.edit', $vehicle) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete_vehicles')
                                                <form action="{{ route('vehicles.destroy', $vehicle) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Affichage de {{ $vehicles->firstItem() }} à {{ $vehicles->lastItem() }}
                            sur {{ $vehicles->total() }} véhicules
                        </div>
                        <div>
                            {{ $vehicles->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-car fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Aucun véhicule trouvé</p>
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
@endsection
