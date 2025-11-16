@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-heartbeat"></i> Visites Médicales</h2>
        @can('create_employees')
        <div>
            <a href="{{ route('medical-checkups.alerts') }}" class="btn btn-warning me-2">
                <i class="fas fa-bell"></i> Alertes
            </a>
            <a href="{{ route('medical-checkups.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Visite
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
                            <h6 class="text-muted mb-1">Total Visites</h6>
                            <h3 class="mb-0">{{ $totalCheckups }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-heartbeat fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Valides</h6>
                            <h3 class="mb-0">{{ $validCheckups }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Expirent Bientôt</h6>
                            <h3 class="mb-0">{{ $expiringSoonCheckups }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Expirées</h6>
                            <h3 class="mb-0">{{ $expiredCheckups }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Employés Aptes</h6>
                            <h3 class="mb-0">{{ $fitEmployees }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-user-check fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Avec Restrictions</h6>
                            <h3 class="mb-0">{{ $withRestrictions }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Inaptes</h6>
                            <h3 class="mb-0">{{ $unfitEmployees }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-user-times fa-2x"></i>
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
            <form method="GET" action="{{ route('medical-checkups.index') }}">
                <div class="row g-3">
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
                        <label class="form-label">Type de Visite</label>
                        <select name="type" class="form-select">
                            <option value="">Tous</option>
                            @foreach($checkupTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Résultat</label>
                        <select name="result" class="form-select">
                            <option value="">Tous</option>
                            @foreach($results as $key => $label)
                                <option value="{{ $key }}" {{ request('result') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous</option>
                            <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Valides</option>
                            <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>Expirent Bientôt</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirées</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('medical-checkups.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Checkups Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Visites Médicales ({{ $checkups->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Date Visite</th>
                            <th>Prochaine Visite</th>
                            <th>Centre Médical</th>
                            <th>Résultat</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checkups as $checkup)
                        <tr>
                            <td>
                                {{ $checkup->employee->first_name }} {{ $checkup->employee->last_name }}
                            </td>
                            <td>{{ $checkup->type_label }}</td>
                            <td>{{ $checkup->checkup_date->format('d/m/Y') }}</td>
                            <td>
                                @if($checkup->next_checkup_date)
                                    {{ $checkup->next_checkup_date->format('d/m/Y') }}
                                    @if($checkup->days_until_expiry)
                                        <br><small class="text-muted">({{ $checkup->days_until_expiry }} jours)</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $checkup->medical_center }}</td>
                            <td>
                                <span class="badge bg-{{ $checkup->result_color }}">
                                    {{ $checkup->result_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $checkup->status_color }}">
                                    {{ $checkup->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('medical-checkups.show', $checkup) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_employees')
                                <a href="{{ route('medical-checkups.edit', $checkup) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete_employees')
                                <form action="{{ route('medical-checkups.destroy', $checkup) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette visite ?');">
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
                            <td colspan="8" class="text-center">Aucune visite médicale trouvée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $checkups->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
