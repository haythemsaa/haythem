@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell"></i> Alertes Visites Médicales</h2>
        <a href="{{ route('medical-checkups.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- Expired Checkups -->
    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-times-circle"></i> Visites Expirées ({{ $expiredCheckups->count() }})</h5>
        </div>
        <div class="card-body">
            @if($expiredCheckups->count() > 0)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>ATTENTION:</strong> Ces employés ont des visites médicales expirées. Planifier une nouvelle visite immédiatement.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Date Expiration</th>
                            <th>Jours Dépassés</th>
                            <th>Dernier Résultat</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiredCheckups as $checkup)
                        <tr>
                            <td>{{ $checkup->employee->first_name }} {{ $checkup->employee->last_name }}</td>
                            <td>{{ $checkup->employee->employee_code }}</td>
                            <td>{{ $checkup->type_label }}</td>
                            <td>{{ $checkup->next_checkup_date->format('d/m/Y') }}</td>
                            <td class="text-danger">
                                <strong>{{ $checkup->next_checkup_date->diffInDays(now()) }} jours</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $checkup->result_color }}">
                                    {{ $checkup->result_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('medical-checkups.show', $checkup) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucune visite expirée</p>
            @endif
        </div>
    </div>

    <!-- Expiring Soon Checkups -->
    <div class="card border-warning mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Visites Expirant Bientôt ({{ $expiringSoonCheckups->count() }})</h5>
        </div>
        <div class="card-body">
            @if($expiringSoonCheckups->count() > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle"></i>
                <strong>ATTENTION:</strong> Ces visites médicales expirent dans les 30 prochains jours. Planifier les rendez-vous.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Date Expiration</th>
                            <th>Jours Restants</th>
                            <th>Dernier Résultat</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringSoonCheckups as $checkup)
                        <tr>
                            <td>{{ $checkup->employee->first_name }} {{ $checkup->employee->last_name }}</td>
                            <td>{{ $checkup->employee->employee_code }}</td>
                            <td>{{ $checkup->type_label }}</td>
                            <td>{{ $checkup->next_checkup_date->format('d/m/Y') }}</td>
                            <td class="text-warning">
                                <strong>{{ $checkup->days_until_expiry }} jours</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $checkup->result_color }}">
                                    {{ $checkup->result_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('medical-checkups.show', $checkup) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucune visite expirant bientôt</p>
            @endif
        </div>
    </div>

    <!-- Unfit Employees -->
    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-user-times"></i> Employés Inaptes ({{ $unfitEmployees->count() }})</h5>
        </div>
        <div class="card-body">
            @if($unfitEmployees->count() > 0)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>CRITIQUE:</strong> Ces employés ont été déclarés inaptes ou temporairement inaptes.
                Vérifier les restrictions et ajuster les affectations.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Code</th>
                            <th>Poste</th>
                            <th>Résultat</th>
                            <th>Date Visite</th>
                            <th>Restrictions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unfitEmployees as $checkup)
                        <tr>
                            <td>{{ $checkup->employee->first_name }} {{ $checkup->employee->last_name }}</td>
                            <td>{{ $checkup->employee->employee_code }}</td>
                            <td>{{ $checkup->employee->job_title ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $checkup->result_color }}">
                                    {{ $checkup->result_label }}
                                </span>
                            </td>
                            <td>{{ $checkup->checkup_date->format('d/m/Y') }}</td>
                            <td>
                                @if($checkup->restrictions)
                                    <span class="badge bg-warning">Oui</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('medical-checkups.show', $checkup) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucun employé inapte</p>
            @endif
        </div>
    </div>
</div>
@endsection
