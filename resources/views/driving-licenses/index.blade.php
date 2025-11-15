@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-id-card"></i> Gestion des Permis de Conduire</h2>
        @can('create_employees')
        <div>
            <a href="{{ route('driving-licenses.alerts') }}" class="btn btn-warning me-2">
                <i class="fas fa-bell"></i> Alertes
            </a>
            <a href="{{ route('driving-licenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Permis
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
                            <h6 class="text-muted mb-1">Total Permis</h6>
                            <h3 class="mb-0">{{ $totalLicenses }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-id-card fa-2x"></i>
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
                            <h3 class="mb-0">{{ $validLicenses }}</h3>
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
                            <h3 class="mb-0">{{ $expiringSoonLicenses }}</h3>
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
                            <h6 class="text-muted mb-1">Expirés</h6>
                            <h3 class="mb-0">{{ $expiredLicenses }}</h3>
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
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Points Faibles (≤6)</h6>
                            <h3 class="mb-0">{{ $lowPointsLicenses }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-minus-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Moyenne Points</h6>
                            <h3 class="mb-0">{{ $averagePoints }} / 12</h3>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-chart-line fa-2x"></i>
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
            <form method="GET" action="{{ route('driving-licenses.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
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
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous</option>
                            <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Valides</option>
                            <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>Expirent Bientôt</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirés</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Points Faibles</label>
                        <select name="low_points" class="form-select">
                            <option value="">Tous</option>
                            <option value="1" {{ request('low_points') == '1' ? 'selected' : '' }}>Oui</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('driving-licenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Licenses Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Permis ({{ $licenses->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>N° Permis</th>
                            <th>Catégories</th>
                            <th>Date Émission</th>
                            <th>Date Expiration</th>
                            <th>Points</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($licenses as $license)
                        <tr>
                            <td>
                                {{ $license->employee->first_name }} {{ $license->employee->last_name }}
                            </td>
                            <td><strong>{{ $license->license_number }}</strong></td>
                            <td>{{ $license->categories_list }}</td>
                            <td>{{ $license->issue_date->format('d/m/Y') }}</td>
                            <td>
                                @if($license->expiry_date)
                                    {{ $license->expiry_date->format('d/m/Y') }}
                                    @if($license->days_until_expiry)
                                        <br><small class="text-muted">({{ $license->days_until_expiry }} jours)</small>
                                    @endif
                                @else
                                    <span class="text-muted">Permanent</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $license->points_color }}">
                                    {{ $license->points }} pts
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $license->status_color }}">
                                    {{ $license->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('driving-licenses.show', $license) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_employees')
                                <a href="{{ route('driving-licenses.edit', $license) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete_employees')
                                <form action="{{ route('driving-licenses.destroy', $license) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce permis ?');">
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
                            <td colspan="8" class="text-center">Aucun permis trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $licenses->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
