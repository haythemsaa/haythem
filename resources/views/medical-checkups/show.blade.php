@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-heartbeat"></i> Détails Visite Médicale</h2>
        <div>
            @can('edit_employees')
            <a href="{{ route('medical-checkups.edit', $medicalCheckup) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('medical-checkups.index') }}" class="btn btn-secondary">
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
            <!-- Employee Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Informations Employé</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Employé:</dt>
                        <dd class="col-sm-9">
                            <a href="{{ route('employees.show', $medicalCheckup->employee) }}">
                                {{ $medicalCheckup->employee->first_name }} {{ $medicalCheckup->employee->last_name }}
                            </a>
                        </dd>

                        <dt class="col-sm-3">Code:</dt>
                        <dd class="col-sm-9">{{ $medicalCheckup->employee->employee_code }}</dd>

                        <dt class="col-sm-3">Poste:</dt>
                        <dd class="col-sm-9">{{ $medicalCheckup->employee->job_title ?? '-' }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Checkup Information -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Détails de la Visite</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-6">Type de Visite:</dt>
                                <dd class="col-sm-6">{{ $medicalCheckup->type_label }}</dd>

                                <dt class="col-sm-6">Date:</dt>
                                <dd class="col-sm-6">{{ $medicalCheckup->checkup_date->format('d/m/Y') }}</dd>

                                <dt class="col-sm-6">Prochaine Visite:</dt>
                                <dd class="col-sm-6">
                                    @if($medicalCheckup->next_checkup_date)
                                        {{ $medicalCheckup->next_checkup_date->format('d/m/Y') }}
                                        @if($medicalCheckup->days_until_expiry)
                                            <br><small class="text-muted">(dans {{ $medicalCheckup->days_until_expiry }} jours)</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Non définie</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-6">Statut:</dt>
                                <dd class="col-sm-6">
                                    <span class="badge bg-{{ $medicalCheckup->status_color }}">
                                        {{ $medicalCheckup->status_label }}
                                    </span>
                                </dd>
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-6">Centre Médical:</dt>
                                <dd class="col-sm-6">{{ $medicalCheckup->medical_center }}</dd>

                                <dt class="col-sm-6">Médecin:</dt>
                                <dd class="col-sm-6">{{ $medicalCheckup->doctor_name ?? '-' }}</dd>

                                <dt class="col-sm-6">Résultat:</dt>
                                <dd class="col-sm-6">
                                    <span class="badge bg-{{ $medicalCheckup->result_color }} fs-6">
                                        {{ $medicalCheckup->result_label }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    @if($medicalCheckup->certificate_file)
                    <div class="mt-3">
                        <a href="{{ route('medical-checkups.download', $medicalCheckup) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Télécharger le Certificat Médical
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Restrictions -->
            @if($medicalCheckup->restrictions)
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-ban"></i> Restrictions Médicales</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $medicalCheckup->restrictions }}</p>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($medicalCheckup->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comment"></i> Remarques</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $medicalCheckup->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header bg-{{ $medicalCheckup->status_color }} text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Statut</h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-1 text-{{ $medicalCheckup->result_color }}">
                        @if($medicalCheckup->result === 'fit')
                            <i class="fas fa-check-circle"></i>
                        @elseif($medicalCheckup->result === 'fit_with_restrictions')
                            <i class="fas fa-exclamation-circle"></i>
                        @else
                            <i class="fas fa-times-circle"></i>
                        @endif
                    </div>
                    <h4 class="mt-3">
                        <span class="badge bg-{{ $medicalCheckup->result_color }} fs-5">
                            {{ $medicalCheckup->result_label }}
                        </span>
                    </h4>

                    @if($medicalCheckup->is_expired)
                    <div class="alert alert-danger mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        La visite médicale a expiré!
                    </div>
                    @elseif($medicalCheckup->is_expiring_soon)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-clock"></i>
                        Expire dans {{ $medicalCheckup->days_until_expiry }} jours
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Chronologie</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="mb-3">
                            <strong>Visite effectuée</strong>
                            <div class="text-muted small">{{ $medicalCheckup->checkup_date->format('d/m/Y') }}</div>
                        </div>

                        @if($medicalCheckup->next_checkup_date)
                        <div class="mb-3">
                            <strong>Prochaine visite prévue</strong>
                            <div class="text-muted small">{{ $medicalCheckup->next_checkup_date->format('d/m/Y') }}</div>
                        </div>
                        @endif

                        <div>
                            <strong>Enregistré le</strong>
                            <div class="text-muted small">{{ $medicalCheckup->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        @if($medicalCheckup->updated_at != $medicalCheckup->created_at)
                        <div class="mt-2">
                            <strong>Dernière modification</strong>
                            <div class="text-muted small">{{ $medicalCheckup->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
