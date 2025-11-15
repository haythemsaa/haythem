@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0"><i class="fas fa-users"></i> Gestion des Employés</h1>
            <p class="text-muted">Liste et gestion des ressources humaines</p>
        </div>
        <div class="col-md-6 text-end">
            @can('create_employees')
                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvel Employé
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
            <form method="GET" action="{{ route('employees.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}"
                               placeholder="Nom, email, code...">
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="suspendu" {{ request('status') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                            <option value="conge" {{ request('status') == 'conge' ? 'selected' : '' }}>Congé</option>
                            <option value="demission" {{ request('status') == 'demission' ? 'selected' : '' }}>Démission</option>
                            <option value="licencie" {{ request('status') == 'licencie' ? 'selected' : '' }}>Licencié</option>
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
                        <label for="department" class="form-label">Département</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">Tous les dép.</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                    {{ ucfirst($dept) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label for="drivers_only" class="form-label">Conducteurs</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="drivers_only"
                                   name="drivers_only" value="1" {{ request('drivers_only') ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Liste des Employés
                <span class="badge bg-light text-dark">{{ $employees->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Nom Complet</th>
                                <th>Poste</th>
                                <th>Département</th>
                                <th>Site</th>
                                <th>Contact</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td><strong>{{ $employee->employee_code }}</strong></td>
                                    <td>
                                        <div>
                                            <strong>{{ $employee->full_name }}</strong>
                                            @if($employee->drivingLicense)
                                                <i class="fas fa-id-card text-success" title="Permis de conduire"></i>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $employee->position ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ ucfirst($employee->position ?? 'N/A') }}</td>
                                    <td>{{ ucfirst($employee->department ?? 'N/A') }}</td>
                                    <td>{{ $employee->site?->name ?? 'Non affecté' }}</td>
                                    <td>
                                        <div class="small">
                                            <div><i class="fas fa-envelope"></i> {{ $employee->email }}</div>
                                            @if($employee->phone)
                                                <div><i class="fas fa-phone"></i> {{ $employee->phone }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($employee->status) {
                                                'actif' => 'success',
                                                'suspendu' => 'warning',
                                                'conge' => 'info',
                                                'demission' => 'secondary',
                                                'licencie' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusLabel = match($employee->status) {
                                                'actif' => 'Actif',
                                                'suspendu' => 'Suspendu',
                                                'conge' => 'Congé',
                                                'demission' => 'Démission',
                                                'licencie' => 'Licencié',
                                                default => $employee->status
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('view_employees')
                                                <a href="{{ route('employees.show', $employee) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('edit_employees')
                                                <a href="{{ route('employees.edit', $employee) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete_employees')
                                                <form action="{{ route('employees.destroy', $employee) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
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
                            Affichage de {{ $employees->firstItem() }} à {{ $employees->lastItem() }}
                            sur {{ $employees->total() }} employés
                        </div>
                        <div>
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Aucun employé trouvé</p>
                    @can('create_employees')
                        <a href="{{ route('employees.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter un employé
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
