@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tire"></i> Détails Pneumatique</h2>
        <div>
            @can('edit_vehicles')
            @if($tire->status === 'installed')
            <a href="{{ route('tires.rotate', $tire) }}" class="btn btn-secondary me-2">
                <i class="fas fa-sync"></i> Rotation
            </a>
            @endif
            <a href="{{ route('tires.edit', $tire) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('tires.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Code Pneu:</dt>
                                <dd class="col-sm-7"><strong>{{ $tire->tire_code }}</strong></dd>

                                <dt class="col-sm-5">Marque:</dt>
                                <dd class="col-sm-7">{{ $tire->brand }}</dd>

                                <dt class="col-sm-5">Modèle:</dt>
                                <dd class="col-sm-7">{{ $tire->model }}</dd>

                                <dt class="col-sm-5">Dimension:</dt>
                                <dd class="col-sm-7"><strong>{{ $tire->size }}</strong></dd>

                                <dt class="col-sm-5">Type:</dt>
                                <dd class="col-sm-7">{{ $tire->tire_type }}</dd>

                                <dt class="col-sm-5">Site:</dt>
                                <dd class="col-sm-7">{{ $tire->site->name ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Véhicule:</dt>
                                <dd class="col-sm-7">
                                    @if($tire->vehicle)
                                        <a href="{{ route('vehicles.show', $tire->vehicle) }}">
                                            {{ $tire->vehicle->registration_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-5">Position:</dt>
                                <dd class="col-sm-7">{{ $tire->position_label }}</dd>

                                <dt class="col-sm-5">Statut:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge bg-{{ $tire->status_color }}">
                                        {{ $tire->status_label }}
                                    </span>
                                </dd>

                                <dt class="col-sm-5">Date d'Achat:</dt>
                                <dd class="col-sm-7">{{ $tire->purchase_date->format('d/m/Y') }}</dd>

                                <dt class="col-sm-5">Date d'Installation:</dt>
                                <dd class="col-sm-7">{{ $tire->installation_date?->format('d/m/Y') ?? '-' }}</dd>

                                <dt class="col-sm-5">Coût d'Achat:</dt>
                                <dd class="col-sm-7"><strong>{{ number_format($tire->purchase_cost, 2) }} DH</strong></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tread Depth & Wear -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-ruler-vertical"></i> Usure & Profondeur de Sculpture</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h6 class="text-muted">Profondeur Initiale</h6>
                                <h3>{{ number_format($tire->initial_tread_depth, 1) }} mm</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h6 class="text-muted">Profondeur Actuelle</h6>
                                <h3 class="text-{{ $tire->wear_color }}">
                                    {{ number_format($tire->current_tread_depth, 1) }} mm
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h6 class="text-muted">Usure</h6>
                                <h3 class="text-{{ $tire->wear_color }}">
                                    {{ number_format($tire->wear_percentage, 0) }}%
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-{{ $tire->wear_color }}"
                             role="progressbar"
                             style="width: {{ $tire->wear_percentage }}%"
                             aria-valuenow="{{ $tire->wear_percentage }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            <strong>{{ number_format($tire->wear_percentage, 0) }}% d'usure</strong>
                        </div>
                    </div>

                    @if($tire->current_tread_depth <= 1.6)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>CRITIQUE:</strong> La profondeur de sculpture est en dessous du minimum légal (1.6 mm).
                            Remplacement immédiat requis!
                        </div>
                    @elseif($tire->current_tread_depth <= 2.0)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-circle"></i>
                            <strong>ATTENTION:</strong> La profondeur de sculpture est faible. Planifier le remplacement prochainement.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mileage -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Kilométrage</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Km à l'Installation</h6>
                                <h4>{{ number_format($tire->mileage_at_installation ?? 0) }} km</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Km Actuel</h6>
                                <h4>{{ number_format($tire->current_mileage ?? 0) }} km</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-muted">Km Parcourus</h6>
                                <h4 class="text-primary">{{ number_format($tire->mileage_traveled) }} km</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($tire->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comment"></i> Remarques</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $tire->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> État du Pneu</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-1 text-{{ $tire->wear_color }}">
                            <i class="fas fa-tire"></i>
                        </div>
                        <h4 class="mt-2">
                            <span class="badge bg-{{ $tire->status_color }} fs-6">
                                {{ $tire->status_label }}
                            </span>
                        </h4>
                    </div>

                    <dl class="row mb-0">
                        <dt class="col-sm-6">État d'Usure:</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-{{ $tire->wear_color }}">
                                {{ ucfirst($tire->wear_status) }}
                            </span>
                        </dd>

                        @if($tire->removal_date)
                        <dt class="col-sm-6">Date Retrait:</dt>
                        <dd class="col-sm-6">{{ $tire->removal_date->format('d/m/Y') }}</dd>

                        <dt class="col-sm-6">Raison:</dt>
                        <dd class="col-sm-6">{{ $tire->removal_reason }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Rotation History -->
            @if($tire->rotations->count() > 0)
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Historique des Rotations</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($tire->rotations as $rotation)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>{{ $rotation->rotation_date->format('d/m/Y') }}</strong>
                                <span class="badge bg-secondary">{{ number_format($rotation->mileage) }} km</span>
                            </div>
                            <div class="small">
                                <i class="fas fa-arrow-right text-primary"></i>
                                {{ $rotation->from_position_label }} → {{ $rotation->to_position_label }}
                            </div>
                            <div class="small text-muted">
                                Profondeur: {{ number_format($rotation->tread_depth_before, 1) }} mm
                                → {{ number_format($rotation->tread_depth_after, 1) }} mm
                            </div>
                            @if($rotation->technician)
                            <div class="small text-muted">
                                Technicien: {{ $rotation->technician }}
                            </div>
                            @endif
                            @if($rotation->notes)
                            <div class="small mt-1">{{ $rotation->notes }}</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
