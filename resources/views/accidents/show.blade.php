@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-car-crash"></i> Détails de l'Accident</h4>
                    <div>
                        @can('edit_accidents')
                        @if($accident->status != 'cloture')
                        <a href="{{ route('accidents.edit', $accident) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        @endif
                        @endcan
                        <a href="{{ route('accidents.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    <!-- Status & Severity Badges -->
                    <div class="mb-4">
                        <span class="badge bg-{{ $accident->severity_color }} fs-6 me-2">
                            Gravité: {{ $accident->severity_label }}
                        </span>
                        <span class="badge bg-{{ $accident->status_color }} fs-6">
                            {{ $accident->status_label }}
                        </span>
                        @if($accident->is_critical)
                        <span class="badge bg-danger fs-6 ms-2">
                            <i class="fas fa-exclamation-triangle"></i> CRITIQUE
                        </span>
                        @endif
                    </div>

                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Date de l'Accident:</th>
                                            <td>{{ $accident->accident_date->format('d/m/Y') }}</td>
                                        </tr>
                                        @if($accident->accident_time)
                                        <tr>
                                            <th>Heure:</th>
                                            <td>{{ \Carbon\Carbon::parse($accident->accident_time)->format('H:i') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Véhicule:</th>
                                            <td>
                                                <strong>{{ $accident->vehicle->registration_number }}</strong>
                                                @if($accident->vehicle->brand)
                                                    <br><small class="text-muted">{{ $accident->vehicle->brand->name }} {{ $accident->vehicle->vehicleModel->name ?? '' }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Conducteur:</th>
                                            <td>
                                                {{ $accident->employee->first_name }} {{ $accident->employee->last_name }}
                                                @if($accident->employee->employee_code)
                                                    <br><small class="text-muted">Code: {{ $accident->employee->employee_code }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Lieu:</th>
                                            <td>{{ $accident->location }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Impact & Costs -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0"><i class="fas fa-user-injured"></i> Impact & Coûts</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Nombre de Blessés:</th>
                                            <td>
                                                @if($accident->injured_count > 0)
                                                    <span class="badge bg-warning">{{ $accident->injured_count }}</span>
                                                @else
                                                    <span class="text-muted">Aucun</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Nombre de Décès:</th>
                                            <td>
                                                @if($accident->fatalities_count > 0)
                                                    <span class="badge bg-dark">{{ $accident->fatalities_count }}</span>
                                                @else
                                                    <span class="text-success">Aucun</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Coût Estimé:</th>
                                            <td>
                                                @if($accident->estimated_cost)
                                                    <strong>{{ number_format($accident->estimated_cost, 2, ',', ' ') }} DH</strong>
                                                @else
                                                    <span class="text-muted">Non estimé</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>N° PV Police:</th>
                                            <td>{{ $accident->police_report_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>N° Déclaration Assurance:</th>
                                            <td>{{ $accident->insurance_claim_number ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-align-left"></i> Description de l'Accident</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $accident->description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Material Damage -->
                        @if($accident->material_damage)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-tools"></i> Dégâts Matériels</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $accident->material_damage }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Third Parties -->
                        @if($accident->third_parties)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-users"></i> Tiers Impliqués</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $accident->third_parties }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Documents -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-file-alt"></i> Documents Associés ({{ $accident->accidentDocuments->count() }})</h6>
                                </div>
                                <div class="card-body">
                                    @if($accident->accidentDocuments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Description</th>
                                                    <th>Date d'Ajout</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($accident->accidentDocuments as $document)
                                                <tr>
                                                    <td>{{ $document->document_type_name }}</td>
                                                    <td>{{ $document->description ?? '-' }}</td>
                                                    <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($document->file_path)
                                                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn btn-sm btn-info" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <p class="text-muted mb-0">Aucun document ajouté.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @can('close_accidents')
                    @if($accident->status == 'en_cours')
                    <div class="mt-4">
                        <form action="{{ route('accidents.close', $accident) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir clôturer cet accident ?');">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Clôturer l'Accident
                            </button>
                        </form>
                    </div>
                    @endif
                    @endcan

                    @can('delete_accidents')
                    <div class="mt-4">
                        <form action="{{ route('accidents.destroy', $accident) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet accident ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer l'Accident
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
