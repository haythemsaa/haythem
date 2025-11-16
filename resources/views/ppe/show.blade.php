@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-hard-hat"></i> Détails EPI</h2>
        <div>
            @can('edit_employees')
            <a href="{{ route('ppe.edit', $ppe) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('ppe.index') }}" class="btn btn-secondary">
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
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Employé:</dt>
                                <dd class="col-sm-7">
                                    <a href="{{ route('employees.show', $ppe->employee) }}">
                                        {{ $ppe->employee->first_name }} {{ $ppe->employee->last_name }}
                                    </a>
                                </dd>

                                <dt class="col-sm-5">Type:</dt>
                                <dd class="col-sm-7">{{ $ppe->equipment_type }}</dd>

                                <dt class="col-sm-5">Nom:</dt>
                                <dd class="col-sm-7"><strong>{{ $ppe->equipment_name }}</strong></dd>

                                <dt class="col-sm-5">Marque:</dt>
                                <dd class="col-sm-7">{{ $ppe->brand ?? '-' }}</dd>

                                <dt class="col-sm-5">Taille:</dt>
                                <dd class="col-sm-7">{{ $ppe->size ?? '-' }}</dd>

                                <dt class="col-sm-5">N° Série:</dt>
                                <dd class="col-sm-7">{{ $ppe->serial_number ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Date Attribution:</dt>
                                <dd class="col-sm-7">{{ $ppe->issue_date->format('d/m/Y') }}</dd>

                                <dt class="col-sm-5">Date Expiration:</dt>
                                <dd class="col-sm-7">
                                    @if($ppe->expiry_date)
                                        {{ $ppe->expiry_date->format('d/m/Y') }}
                                        @if($ppe->days_until_expiry)
                                            <br><small>(dans {{ $ppe->days_until_expiry }} jours)</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-5">État:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge bg-{{ $ppe->condition_color }}">{{ $ppe->condition_label }}</span>
                                </dd>

                                <dt class="col-sm-5">Statut:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge bg-{{ $ppe->status_color }}">{{ $ppe->status_label }}</span>
                                </dd>

                                <dt class="col-sm-5">Coût:</dt>
                                <dd class="col-sm-7">
                                    @if($ppe->cost)
                                        {{ number_format($ppe->cost, 2) }} DH
                                    @else
                                        -
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            @if($ppe->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comment"></i> Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $ppe->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-{{ $ppe->expiry_status_color }} text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Statut</h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-1 text-{{ $ppe->condition_color }}">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                    <h4 class="mt-3">
                        <span class="badge bg-{{ $ppe->status_color }} fs-5">{{ $ppe->status_label }}</span>
                    </h4>

                    @if($ppe->is_expired)
                    <div class="alert alert-danger mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Expiré - Remplacement requis
                    </div>
                    @elseif($ppe->is_expiring_soon)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-clock"></i> Expire dans {{ $ppe->days_until_expiry }} jours
                    </div>
                    @endif

                    @if($ppe->condition === 'damaged')
                    <div class="alert alert-danger mt-3 mb-0">
                        <i class="fas fa-tools"></i> Équipement endommagé
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
