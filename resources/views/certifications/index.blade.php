@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-certificate"></i> Gestion des Certifications</h2>
        @can('create_employees')
        <div>
            <a href="{{ route('certifications.alerts') }}" class="btn btn-warning me-2">
                <i class="fas fa-bell"></i> Alertes
            </a>
            <a href="{{ route('certifications.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Certification
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
                            <h6 class="text-muted mb-1">Total Certifications</h6>
                            <h3 class="mb-0">{{ $totalCertifications }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-certificate fa-2x"></i>
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
                            <h3 class="mb-0">{{ $validCertifications }}</h3>
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
                            <h3 class="mb-0">{{ $expiringSoonCertifications }}</h3>
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
                            <h3 class="mb-0">{{ $expiredCertifications }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
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
            <form method="GET" action="{{ route('certifications.index') }}">
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
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">Tous les types</option>
                            @foreach($certificationTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $key }}
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
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirées</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('certifications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Certifications Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Certifications ({{ $certifications->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>N° Certification</th>
                            <th>Organisme</th>
                            <th>Date Émission</th>
                            <th>Date Expiration</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certifications as $certification)
                        <tr>
                            <td>{{ $certification->employee->first_name }} {{ $certification->employee->last_name }}</td>
                            <td><strong>{{ $certification->name }}</strong></td>
                            <td>{{ $certification->certification_number ?? '-' }}</td>
                            <td>{{ Str::limit($certification->issuing_organization ?? '-', 30) }}</td>
                            <td>{{ $certification->issue_date->format('d/m/Y') }}</td>
                            <td>
                                @if($certification->expiry_date)
                                    {{ $certification->expiry_date->format('d/m/Y') }}
                                    @if($certification->days_until_expiry)
                                        <br><small class="text-muted">({{ $certification->days_until_expiry }} jours)</small>
                                    @endif
                                @else
                                    <span class="text-muted">Permanent</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $certification->status_color }}">
                                    {{ $certification->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('certifications.show', $certification) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_employees')
                                <a href="{{ route('certifications.edit', $certification) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete_employees')
                                <form action="{{ route('certifications.destroy', $certification) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette certification ?');">
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
                            <td colspan="8" class="text-center">Aucune certification trouvée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $certifications->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
