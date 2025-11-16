@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell"></i> Alertes Usure Pneumatiques</h2>
        <a href="{{ route('tires.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- Critical Wear Tires -->
    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-times-circle"></i> Usure Critique - Remplacement Immédiat ({{ $criticalWearTires->count() }})</h5>
        </div>
        <div class="card-body">
            @if($criticalWearTires->count() > 0)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>ATTENTION:</strong> Ces pneus ont une profondeur de sculpture ≤ 2.0 mm et nécessitent un remplacement immédiat.
                Le minimum légal est de 1.6 mm.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Code Pneu</th>
                            <th>Véhicule</th>
                            <th>Position</th>
                            <th>Marque / Modèle</th>
                            <th>Profondeur Actuelle</th>
                            <th>Usure</th>
                            <th>Km Parcourus</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criticalWearTires as $tire)
                        <tr class="{{ $tire->current_tread_depth <= 1.6 ? 'table-danger' : '' }}">
                            <td><strong>{{ $tire->tire_code }}</strong></td>
                            <td>
                                @if($tire->vehicle)
                                    {{ $tire->vehicle->registration_number }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $tire->position_label }}</td>
                            <td>{{ $tire->brand }} {{ $tire->model }}</td>
                            <td>
                                <span class="badge bg-danger fs-6">
                                    {{ number_format($tire->current_tread_depth, 1) }} mm
                                </span>
                                @if($tire->current_tread_depth <= 1.6)
                                    <span class="badge bg-dark ms-1">ILLEGAL</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 20px; min-width: 100px;">
                                    <div class="progress-bar bg-danger"
                                         role="progressbar"
                                         style="width: {{ $tire->wear_percentage }}%"
                                         aria-valuenow="{{ $tire->wear_percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ number_format($tire->wear_percentage, 0) }}%
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format($tire->mileage_traveled) }} km</td>
                            <td>
                                <a href="{{ route('tires.show', $tire) }}" class="btn btn-sm btn-info">Voir</a>
                                @can('edit_vehicles')
                                <a href="{{ route('tires.edit', $tire) }}" class="btn btn-sm btn-warning">Modifier</a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0">
                <i class="fas fa-check-circle"></i> Aucun pneu en usure critique
            </p>
            @endif
        </div>
    </div>

    <!-- Low Wear Tires -->
    <div class="card border-warning mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Usure Faible - Surveillance Requise ({{ $lowWearTires->count() }})</h5>
        </div>
        <div class="card-body">
            @if($lowWearTires->count() > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle"></i>
                <strong>ATTENTION:</strong> Ces pneus ont une profondeur de sculpture entre 2.0 mm et 3.0 mm.
                Planifier le remplacement prochainement.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Code Pneu</th>
                            <th>Véhicule</th>
                            <th>Position</th>
                            <th>Marque / Modèle</th>
                            <th>Profondeur Actuelle</th>
                            <th>Usure</th>
                            <th>Km Parcourus</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowWearTires as $tire)
                        <tr>
                            <td><strong>{{ $tire->tire_code }}</strong></td>
                            <td>
                                @if($tire->vehicle)
                                    {{ $tire->vehicle->registration_number }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $tire->position_label }}</td>
                            <td>{{ $tire->brand }} {{ $tire->model }}</td>
                            <td>
                                <span class="badge bg-warning fs-6">
                                    {{ number_format($tire->current_tread_depth, 1) }} mm
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px; min-width: 100px;">
                                    <div class="progress-bar bg-warning"
                                         role="progressbar"
                                         style="width: {{ $tire->wear_percentage }}%"
                                         aria-valuenow="{{ $tire->wear_percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ number_format($tire->wear_percentage, 0) }}%
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format($tire->mileage_traveled) }} km</td>
                            <td>
                                <a href="{{ route('tires.show', $tire) }}" class="btn btn-sm btn-info">Voir</a>
                                @can('edit_vehicles')
                                <a href="{{ route('tires.edit', $tire) }}" class="btn btn-sm btn-warning">Modifier</a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0">
                <i class="fas fa-check-circle"></i> Aucun pneu en usure faible
            </p>
            @endif
        </div>
    </div>

    <!-- Summary -->
    <div class="card border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Résumé</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="text-muted">Usure Critique</h6>
                        <h3 class="text-danger">{{ $criticalWearTires->count() }}</h3>
                        <p class="small mb-0">≤ 2.0 mm</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="text-muted">Usure Faible</h6>
                        <h3 class="text-warning">{{ $lowWearTires->count() }}</h3>
                        <p class="small mb-0">2.0 - 3.0 mm</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="text-muted">Total Alertes</h6>
                        <h3 class="text-primary">{{ $criticalWearTires->count() + $lowWearTires->count() }}</h3>
                        <p class="small mb-0">Pneus à surveiller</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
