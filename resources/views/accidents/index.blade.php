@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-car-crash"></i> Gestion des Accidents</h2>
        @can('create_accidents')
        <a href="{{ route('accidents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Accident
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
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
                            <h6 class="text-muted mb-1">Total Accidents</h6>
                            <h3 class="mb-0">{{ $totalAccidents }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-car-crash fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Critiques</h6>
                            <h3 class="mb-0">{{ $criticalAccidents }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <h6 class="text-muted mb-1">En Cours</h6>
                            <h3 class="mb-0">{{ $inProgressAccidents }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-hourglass-half fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Clôturés</h6>
                            <h3 class="mb-0">{{ $closedAccidents }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Blessés</h6>
                            <h3 class="mb-0">{{ $totalInjuries }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-user-injured fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Décès</h6>
                            <h3 class="mb-0">{{ $totalFatalities }}</h3>
                        </div>
                        <div class="text-dark">
                            <i class="fas fa-heartbeat fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Coût Estimé</h6>
                            <h3 class="mb-0">{{ number_format($totalEstimatedCost, 0, ',', ' ') }} DH</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres de Recherche</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('accidents.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Véhicule</label>
                        <select name="vehicle_id" class="form-select">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Employé</label>
                        <select name="employee_id" class="form-select">
                            <option value="">Tous les employés</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Gravité</label>
                        <select name="severity" class="form-select">
                            <option value="">Toutes</option>
                            @foreach($severities as $key => $label)
                                <option value="{{ $key }}" {{ request('severity') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Critiques uniquement</label>
                        <select name="critical_only" class="form-select">
                            <option value="">Non</option>
                            <option value="1" {{ request('critical_only') == '1' ? 'selected' : '' }}>Oui</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date début</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Lieu, N° PV, N° Sinistre..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('accidents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Accidents Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Accidents ({{ $accidents->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Véhicule</th>
                            <th>Employé</th>
                            <th>Lieu</th>
                            <th>Gravité</th>
                            <th>Blessés/Décès</th>
                            <th>Coût Estimé</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accidents as $accident)
                        <tr>
                            <td>
                                {{ $accident->accident_date->format('d/m/Y') }}
                                @if($accident->accident_time)
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($accident->accident_time)->format('H:i') }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $accident->vehicle->registration_number }}</strong>
                            </td>
                            <td>
                                {{ $accident->employee->first_name }} {{ $accident->employee->last_name }}
                            </td>
                            <td>{{ Str::limit($accident->location, 30) }}</td>
                            <td>
                                <span class="badge bg-{{ $accident->severity_color }}">
                                    {{ $accident->severity_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($accident->injured_count > 0)
                                    <span class="badge bg-warning">{{ $accident->injured_count }} blessé(s)</span>
                                @endif
                                @if($accident->fatalities_count > 0)
                                    <span class="badge bg-dark">{{ $accident->fatalities_count }} décès</span>
                                @endif
                                @if($accident->injured_count == 0 && $accident->fatalities_count == 0)
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($accident->estimated_cost)
                                    {{ number_format($accident->estimated_cost, 0, ',', ' ') }} DH
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $accident->status_color }}">
                                    {{ $accident->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('accidents.show', $accident) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_accidents')
                                <a href="{{ route('accidents.edit', $accident) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete_accidents')
                                <form action="{{ route('accidents.destroy', $accident) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet accident ?');">
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
                            <td colspan="9" class="text-center">Aucun accident trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $accidents->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
