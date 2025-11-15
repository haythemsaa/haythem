@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-exclamation-circle"></i> Détails de l'Infraction</h4>
                    <div>
                        @can('edit_violations')
                        <a href="{{ route('traffic-violations.edit', $trafficViolation) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        @endcan
                        <a href="{{ route('traffic-violations.index') }}" class="btn btn-secondary btn-sm">
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

                    <!-- Severity Badge -->
                    <div class="mb-4">
                        <span class="badge bg-{{ $trafficViolation->severity_color }} fs-6">
                            Gravité: {{ ucfirst($trafficViolation->severity_level) }}
                        </span>
                        @if($trafficViolation->points_deducted > 0)
                        <span class="badge bg-danger fs-6 ms-2">
                            <i class="fas fa-minus-circle"></i> {{ $trafficViolation->points_deducted }} Points
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
                                            <th width="40%">Date de l'Infraction:</th>
                                            <td>{{ $trafficViolation->violation_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type d'Infraction:</th>
                                            <td><strong>{{ $trafficViolation->violation_type_name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Conducteur:</th>
                                            <td>
                                                {{ $trafficViolation->employee->first_name }} {{ $trafficViolation->employee->last_name }}
                                                @if($trafficViolation->employee->employee_code)
                                                    <br><small class="text-muted">Code: {{ $trafficViolation->employee->employee_code }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Véhicule:</th>
                                            <td>
                                                @if($trafficViolation->vehicle)
                                                    <strong>{{ $trafficViolation->vehicle->registration_number }}</strong>
                                                    @if($trafficViolation->vehicle->brand)
                                                        <br><small class="text-muted">{{ $trafficViolation->vehicle->brand->name }} {{ $trafficViolation->vehicle->vehicleModel->name ?? '' }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Non spécifié</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Lieu:</th>
                                            <td>{{ $trafficViolation->location ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Impact -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Impact Financier</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Montant Amende:</th>
                                            <td>
                                                @if($trafficViolation->fine_amount)
                                                    <strong>{{ number_format($trafficViolation->fine_amount, 2, ',', ' ') }} DH</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Frais Additionnels:</th>
                                            <td>
                                                @if($trafficViolation->additional_costs)
                                                    {{ number_format($trafficViolation->additional_costs, 2, ',', ' ') }} DH
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><strong>Coût Total:</strong></th>
                                            <td>
                                                <strong class="text-danger">
                                                    {{ number_format($trafficViolation->total_cost, 2, ',', ' ') }} DH
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Points Retirés:</th>
                                            <td>
                                                @if($trafficViolation->points_deducted > 0)
                                                    <span class="badge bg-danger">{{ $trafficViolation->points_deducted }} points</span>
                                                @else
                                                    <span class="text-success">Aucun</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Consequences -->
                        @if($trafficViolation->consequences)
                        <div class="col-md-12 mb-4">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Conséquences</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $trafficViolation->consequences }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Attachment -->
                        @if($trafficViolation->attachment)
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-file-alt"></i> Pièce Jointe</h6>
                                </div>
                                <div class="card-body">
                                    <a href="{{ Storage::url($trafficViolation->attachment) }}" target="_blank" class="btn btn-info">
                                        <i class="fas fa-eye"></i> Voir le document
                                    </a>
                                    <small class="text-muted ms-2">{{ basename($trafficViolation->attachment) }}</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Audit Information -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-clock"></i> Informations de Suivi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Date d'enregistrement:</strong></p>
                                            <p class="text-muted">{{ $trafficViolation->created_at->format('d/m/Y à H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Dernière modification:</strong></p>
                                            <p class="text-muted">{{ $trafficViolation->updated_at->format('d/m/Y à H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @can('delete_violations')
                    <div class="mt-4">
                        <form action="{{ route('traffic-violations.destroy', $trafficViolation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette infraction ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer l'Infraction
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
