@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-hard-hat"></i> Équipements de Protection Individuelle (EPI)</h2>
        @can('create_employees')
        <div>
            <a href="{{ route('ppe.alerts') }}" class="btn btn-warning me-2">
                <i class="fas fa-bell"></i> Alertes
            </a>
            <a href="{{ route('ppe.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvel EPI
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
                            <h6 class="text-muted mb-1">Total EPI</h6>
                            <h3 class="mb-0">{{ $totalEquipment }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-hard-hat fa-2x"></i>
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
                            <h6 class="text-muted mb-1">En Service</h6>
                            <h3 class="mb-0">{{ $inUseEquipment }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h3 class="mb-0">{{ $expiredEquipment }}</h3>
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
                            <h6 class="text-muted mb-1">À Remplacer Bientôt</h6>
                            <h3 class="mb-0">{{ $expiringSoonEquipment }}</h3>
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
                            <h6 class="text-muted mb-1">Endommagés</h6>
                            <h3 class="mb-0">{{ $damagedEquipment }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-tools fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Perdus</h6>
                            <h3 class="mb-0">{{ $lostEquipment }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-search fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Retirés</h6>
                            <h3 class="mb-0">{{ $retiredEquipment }}</h3>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-archive fa-2x"></i>
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
            <form method="GET" action="{{ route('ppe.index') }}">
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
                    <div class="col-md-2">
                        <label class="form-label">Type EPI</label>
                        <select name="equipment_type" class="form-select">
                            <option value="">Tous</option>
                            @foreach($equipmentTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('equipment_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">État</label>
                        <select name="condition" class="form-select">
                            <option value="">Tous</option>
                            @foreach($conditions as $key => $label)
                                <option value="{{ $key }}" {{ request('condition') == $key ? 'selected' : '' }}>
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
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('ppe.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des EPI ({{ $equipment->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Nom / Marque</th>
                            <th>Date Attribution</th>
                            <th>Date Expiration</th>
                            <th>État</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipment as $item)
                        <tr>
                            <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                            <td>{{ $item->equipment_type }}</td>
                            <td>
                                <strong>{{ $item->equipment_name }}</strong>
                                @if($item->brand)
                                    <br><small class="text-muted">{{ $item->brand }}</small>
                                @endif
                            </td>
                            <td>{{ $item->issue_date->format('d/m/Y') }}</td>
                            <td>
                                @if($item->expiry_date)
                                    {{ $item->expiry_date->format('d/m/Y') }}
                                    @if($item->days_until_expiry)
                                        <br><small class="text-muted">({{ $item->days_until_expiry }} jours)</small>
                                    @endif
                                    <br><span class="badge bg-{{ $item->expiry_status_color }}">
                                        @if($item->is_expired) Expiré
                                        @elseif($item->is_expiring_soon) Expire bientôt
                                        @else Valide
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $item->condition_color }}">
                                    {{ $item->condition_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $item->status_color }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('ppe.show', $item) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_employees')
                                <a href="{{ route('ppe.edit', $item) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete_employees')
                                <form action="{{ route('ppe.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet EPI ?');">
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
                            <td colspan="8" class="text-center">Aucun EPI trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $equipment->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
