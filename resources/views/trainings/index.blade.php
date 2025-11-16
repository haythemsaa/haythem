@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-graduation-cap"></i> Gestion des Formations</h2>
        @can('create_trainings')
        <a href="{{ route('trainings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Formation
        </a>
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
                            <h6 class="text-muted mb-1">Total Formations</h6>
                            <h3 class="mb-0">{{ $totalTrainings }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-graduation-cap fa-2x"></i>
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
                            <h6 class="text-muted mb-1">En Cours</h6>
                            <h3 class="mb-0">{{ $ongoingTrainings }}</h3>
                        </div>
                        <div class="text-info">
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
                            <h6 class="text-muted mb-1">Terminées</h6>
                            <h3 class="mb-0">{{ $completedTrainings }}</h3>
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
                            <h6 class="text-muted mb-1">À Venir</h6>
                            <h3 class="mb-0">{{ $upcomingTrainings }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Formations Réussies</h6>
                            <h3 class="mb-0">{{ $successfulTrainings }}</h3>
                        </div>
                        <div class="text-secondary">
                            <i class="fas fa-award fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Heures</h6>
                            <h3 class="mb-0">{{ number_format($totalHours) }} h</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Coût Total</h6>
                            <h3 class="mb-0">{{ number_format($totalCost, 0, ',', ' ') }} DH</h3>
                        </div>
                        <div class="text-danger">
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
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('trainings.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Employé</label>
                        <select name="employee_id" class="form-select">
                            <option value="">Tous</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">Tous</option>
                            @foreach($trainingTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>À Venir</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>En Cours</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminées</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Résultat</label>
                        <select name="result" class="form-select">
                            <option value="">Tous</option>
                            @foreach($results as $key => $label)
                                <option value="{{ $key }}" {{ request('result') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('trainings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Trainings Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Formations ({{ $trainings->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Organisation</th>
                            <th>Dates</th>
                            <th>Durée</th>
                            <th>Coût</th>
                            <th>Statut</th>
                            <th>Résultat</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings as $training)
                        <tr>
                            <td><strong>{{ $training->title }}</strong></td>
                            <td>{{ $training->employee->first_name }} {{ $training->employee->last_name }}</td>
                            <td>{{ $training->training_type }}</td>
                            <td>{{ Str::limit($training->organization, 25) }}</td>
                            <td>
                                {{ $training->start_date->format('d/m/Y') }}
                                @if($training->end_date)
                                    <br><small class="text-muted">au {{ $training->end_date->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>{{ $training->duration_hours ? $training->duration_hours . ' h' : '-' }}</td>
                            <td>{{ $training->cost ? number_format($training->cost, 0, ',', ' ') . ' DH' : '-' }}</td>
                            <td><span class="badge bg-{{ $training->status_color }}">{{ $training->status_label }}</span></td>
                            <td>
                                @if($training->result_label)
                                    <span class="badge bg-{{ $training->result_color }}">{{ $training->result_label }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('trainings.show', $training) }}" class="btn btn-sm btn-info" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_trainings')
                                <a href="{{ route('trainings.edit', $training) }}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete_trainings')
                                <form action="{{ route('trainings.destroy', $training) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette formation ?');">
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
                            <td colspan="10" class="text-center">Aucune formation trouvée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $trainings->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
