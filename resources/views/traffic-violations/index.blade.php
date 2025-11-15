@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-exclamation-circle"></i> Gestion des Infractions Routières</h2>
        @can('create_violations')
        <a href="{{ route('traffic-violations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Infraction
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
                            <h6 class="text-muted mb-1">Total Infractions</h6>
                            <h3 class="mb-0">{{ $totalViolations }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Haute Gravité</h6>
                            <h3 class="mb-0">{{ $highSeverityViolations }}</h3>
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
                            <h6 class="text-muted mb-1">Total Amendes</h6>
                            <h3 class="mb-0">{{ number_format($totalFines, 0, ',', ' ') }} DH</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Total Points</h6>
                            <h3 class="mb-0">{{ $totalPoints }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-minus-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Avec Retrait de Points</h6>
                            <h3 class="mb-0">{{ $withPointsViolations }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-id-card fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Coûts Additionnels</h6>
                            <h3 class="mb-0">{{ number_format($totalAdditionalCosts, 0, ',', ' ') }} DH</h3>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-plus-circle fa-2x"></i>
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
            <form method="GET" action="{{ route('traffic-violations.index') }}">
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
                    <div class="col-md-3">
                        <label class="form-label">Type d'Infraction</label>
                        <select name="violation_type" class="form-select">
                            <option value="">Tous les types</option>
                            @foreach($violationTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('violation_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Haute Gravité</label>
                        <select name="high_severity_only" class="form-select">
                            <option value="">Toutes</option>
                            <option value="1" {{ request('high_severity_only') == '1' ? 'selected' : '' }}>Oui</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Avec Points</label>
                        <select name="with_points_only" class="form-select">
                            <option value="">Toutes</option>
                            <option value="1" {{ request('with_points_only') == '1' ? 'selected' : '' }}>Oui</option>
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
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('traffic-violations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Violations Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Infractions ({{ $violations->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employé</th>
                            <th>Véhicule</th>
                            <th>Type d'Infraction</th>
                            <th>Lieu</th>
                            <th>Amende</th>
                            <th>Points</th>
                            <th>Gravité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($violations as $violation)
                        <tr>
                            <td>{{ $violation->violation_date->format('d/m/Y') }}</td>
                            <td>
                                {{ $violation->employee->first_name }} {{ $violation->employee->last_name }}
                            </td>
                            <td>
                                @if($violation->vehicle)
                                    <strong>{{ $violation->vehicle->registration_number }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $violation->violation_type_name }}</td>
                            <td>{{ Str::limit($violation->location ?? '-', 30) }}</td>
                            <td>
                                @if($violation->fine_amount)
                                    {{ number_format($violation->fine_amount, 0, ',', ' ') }} DH
                                    @if($violation->additional_costs)
                                        <br><small class="text-muted">+ {{ number_format($violation->additional_costs, 0, ',', ' ') }} DH</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($violation->points_deducted > 0)
                                    <span class="badge bg-danger">{{ $violation->points_deducted }} pts</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $violation->severity_color }}">
                                    {{ ucfirst($violation->severity_level) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('traffic-violations.show', $violation) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_violations')
                                <a href="{{ route('traffic-violations.edit', $violation) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete_violations')
                                <form action="{{ route('traffic-violations.destroy', $violation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette infraction ?');">
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
                            <td colspan="9" class="text-center">Aucune infraction trouvée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $violations->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
