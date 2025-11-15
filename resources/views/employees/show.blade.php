@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-user"></i> {{ $employee->full_name }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employés</a></li>
                    <li class="breadcrumb-item active">{{ $employee->employee_code }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            @can('edit_employees')
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            @endcan
            @can('delete_employees')
                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Status Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h4 class="mb-0">{{ $employee->employee_code }}</h4>
                            <p class="text-muted mb-0">Code Employé</p>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0">{{ ucfirst($employee->position ?? 'N/A') }}</h5>
                            <p class="text-muted mb-0">Poste</p>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0">{{ $employee->site?->name ?? 'Non affecté' }}</h5>
                            <p class="text-muted mb-0">Site</p>
                        </div>
                        <div class="col-md-3">
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
                            <h5 class="mb-0"><span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span></h5>
                            <p class="text-muted mb-0">Statut</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-6">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Informations Personnelles</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Code Employé</th>
                                <td><strong>{{ $employee->employee_code }}</strong></td>
                            </tr>
                            <tr>
                                <th>Nom Complet</th>
                                <td><strong>{{ $employee->full_name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Date de Naissance</th>
                                <td>{{ $employee->birth_date?->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Adresse</th>
                                <td>{{ $employee->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Ville</th>
                                <td>{{ $employee->city ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Pays</th>
                                <td>{{ $employee->country ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Contact -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-address-book"></i> Contact</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Email</th>
                                <td><a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a></td>
                            </tr>
                            <tr>
                                <th>Téléphone</th>
                                <td>{{ $employee->phone ?? 'N/A' }}</td>
                            </tr>
                            @if($employee->user)
                            <tr>
                                <th>Compte Utilisateur</th>
                                <td><span class="badge bg-success">Actif</span></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <!-- Professional Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-briefcase"></i> Informations Professionnelles</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Poste</th>
                                <td>{{ ucfirst($employee->position ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <th>Département</th>
                                <td>{{ ucfirst($employee->department ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <th>Site</th>
                                <td>{{ $employee->site?->name ?? 'Non affecté' }}</td>
                            </tr>
                            <tr>
                                <th>Superviseur</th>
                                <td>{{ $employee->supervisor?->full_name ?? 'Aucun' }}</td>
                            </tr>
                            <tr>
                                <th>Date d'Embauche</th>
                                <td>{{ $employee->hire_date?->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Type de Contrat</th>
                                <td>{{ $employee->contract_type ? strtoupper($employee->contract_type) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Salaire de Base</th>
                                <td>
                                    @if($employee->base_salary)
                                        <strong>{{ number_format($employee->base_salary, 2, ',', ' ') }} €</strong>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Driving License -->
            @if($employee->drivingLicense)
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-id-card"></i> Permis de Conduire</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th width="40%">Numéro</th>
                                <td>{{ $employee->drivingLicense->license_number }}</td>
                            </tr>
                            <tr>
                                <th>Catégories</th>
                                <td>{{ $employee->drivingLicense->categories }}</td>
                            </tr>
                            <tr>
                                <th>Date d'Expiration</th>
                                <td>{{ $employee->drivingLicense->expiry_date?->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Tabs for Related Data -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="subordinates-tab" data-bs-toggle="tab"
                                    data-bs-target="#subordinates" type="button" role="tab">
                                <i class="fas fa-users"></i> Subordonnés
                                <span class="badge bg-secondary">{{ $employee->subordinates->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="certifications-tab" data-bs-toggle="tab"
                                    data-bs-target="#certifications" type="button" role="tab">
                                <i class="fas fa-certificate"></i> Certifications
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="violations-tab" data-bs-toggle="tab"
                                    data-bs-target="#violations" type="button" role="tab">
                                <i class="fas fa-exclamation-triangle"></i> Infractions
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assignments-tab" data-bs-toggle="tab"
                                    data-bs-target="#assignments" type="button" role="tab">
                                <i class="fas fa-car"></i> Affectations Véhicules
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="subordinates" role="tabpanel">
                            @if($employee->subordinates->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Nom</th>
                                                <th>Poste</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee->subordinates as $subordinate)
                                                <tr>
                                                    <td>{{ $subordinate->employee_code }}</td>
                                                    <td>{{ $subordinate->full_name }}</td>
                                                    <td>{{ ucfirst($subordinate->position ?? 'N/A') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $subordinate->is_active ? 'success' : 'secondary' }}">
                                                            {{ $subordinate->is_active ? 'Actif' : 'Inactif' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted"><i class="fas fa-info-circle"></i> Aucun subordonné</p>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="certifications" role="tabpanel">
                            <p class="text-muted"><i class="fas fa-info-circle"></i> Les certifications seront affichées ici.</p>
                        </div>
                        <div class="tab-pane fade" id="violations" role="tabpanel">
                            <p class="text-muted"><i class="fas fa-info-circle"></i> Les infractions seront affichées ici.</p>
                        </div>
                        <div class="tab-pane fade" id="assignments" role="tabpanel">
                            <p class="text-muted"><i class="fas fa-info-circle"></i> Les affectations de véhicules seront affichées ici.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
