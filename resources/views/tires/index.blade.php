@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tire"></i> Gestion des Pneumatiques</h2>
        @can('create_vehicles')
        <div>
            <a href="{{ route('tires.alerts') }}" class="btn btn-warning me-2">
                <i class="fas fa-bell"></i> Alertes Usure
            </a>
            <a href="{{ route('tires.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Pneumatique
            </a>
        </div>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Pneus</h6>
                            <h3 class="mb-0">{{ $totalTires }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-tire fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Installés</h6>
                            <h3 class="mb-0">{{ $installedTires }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">En Stock</h6>
                            <h3 class="mb-0">{{ $stockTires }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-warehouse fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Usés</h6>
                            <h3 class="mb-0">{{ $wornOutTires }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Usure Critique</h6>
                            <h3 class="mb-0">{{ $criticalWearTires }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Usure Faible</h6>
                            <h3 class="mb-0">{{ $lowWearTires }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Profondeur Moyenne</h6>
                            <h3 class="mb-0">{{ number_format($averageWear ?? 0, 1) }} mm</h3>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-ruler-vertical fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Coût Total</h6>
                            <h3 class="mb-0">{{ number_format($totalCost, 0) }} DH</h3>
                        </div>
                        <div class="text-dark">
                            <i class="fas fa-coins fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tires.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Véhicule</label>
                        <select name="vehicle_id" class="form-select">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous</option>
                            @foreach($tireStatuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Position</label>
                        <select name="position" class="form-select">
                            <option value="">Toutes</option>
                            @foreach($tirePositions as $key => $label)
                                <option value="{{ $key }}" {{ request('position') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Usure</label>
                        <select name="wear_status" class="form-select">
                            <option value="">Tous</option>
                            <option value="critical" {{ request('wear_status') == 'critical' ? 'selected' : '' }}>Critique</option>
                            <option value="warning" {{ request('wear_status') == 'warning' ? 'selected' : '' }}>Faible</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('tires.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tires Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Pneumatiques ({{ $tires->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Marque / Modèle</th>
                            <th>Taille</th>
                            <th>Véhicule</th>
                            <th>Position</th>
                            <th>Profondeur</th>
                            <th>Usure</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tires as $tire)
                        <tr>
                            <td><strong>{{ $tire->tire_code }}</strong></td>
                            <td>{{ $tire->brand }} {{ $tire->model }}</td>
                            <td>{{ $tire->size }}</td>
                            <td>
                                @if($tire->vehicle)
                                    {{ $tire->vehicle->registration_number }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $tire->position_label }}</td>
                            <td>
                                <span class="badge bg-{{ $tire->wear_color }}">
                                    {{ number_format($tire->current_tread_depth, 1) }} mm
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $tire->wear_color }}"
                                         role="progressbar"
                                         style="width: {{ $tire->wear_percentage }}%"
                                         aria-valuenow="{{ $tire->wear_percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ number_format($tire->wear_percentage, 0) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $tire->status_color }}">
                                    {{ $tire->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('tires.show', $tire) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_vehicles')
                                <a href="{{ route('tires.edit', $tire) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($tire->status === 'installed')
                                <a href="{{ route('tires.rotate', $tire) }}" class="btn btn-sm btn-secondary" title="Rotation">
                                    <i class="fas fa-sync"></i>
                                </a>
                                @endif
                                @endcan
                                @can('delete_vehicles')
                                <form action="{{ route('tires.destroy', $tire) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce pneu ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Aucun pneumatique trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $tires->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
