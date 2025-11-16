@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell"></i> Alertes Certifications</h2>
        <a href="{{ route('certifications.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- Expired Certifications -->
    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-times-circle"></i> Certifications Expirées ({{ $expiredCertifications->count() }})</h5>
        </div>
        <div class="card-body">
            @if($expiredCertifications->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>N° Certification</th>
                            <th>Date Expiration</th>
                            <th>Jours Dépassés</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiredCertifications as $certification)
                        <tr>
                            <td>{{ $certification->employee->first_name }} {{ $certification->employee->last_name }}</td>
                            <td>{{ $certification->name }}</td>
                            <td>{{ $certification->certification_number ?? '-' }}</td>
                            <td>{{ $certification->expiry_date->format('d/m/Y') }}</td>
                            <td class="text-danger"><strong>{{ $certification->expiry_date->diffInDays(now()) }} jours</strong></td>
                            <td>
                                <a href="{{ route('certifications.show', $certification) }}" class="btn btn-sm btn-info">Voir</a>
                                @can('edit_employees')
                                <a href="{{ route('certifications.edit', $certification) }}" class="btn btn-sm btn-warning">Renouveler</a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucune certification expirée</p>
            @endif
        </div>
    </div>

    <!-- Expiring Soon Certifications -->
    <div class="card border-warning">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Certifications Expirant Bientôt ({{ $expiringSoonCertifications->count() }})</h5>
        </div>
        <div class="card-body">
            @if($expiringSoonCertifications->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>N° Certification</th>
                            <th>Date Expiration</th>
                            <th>Jours Restants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringSoonCertifications as $certification)
                        <tr>
                            <td>{{ $certification->employee->first_name }} {{ $certification->employee->last_name }}</td>
                            <td>{{ $certification->name }}</td>
                            <td>{{ $certification->certification_number ?? '-' }}</td>
                            <td>{{ $certification->expiry_date->format('d/m/Y') }}</td>
                            <td class="text-warning"><strong>{{ $certification->days_until_expiry }} jours</strong></td>
                            <td>
                                <a href="{{ route('certifications.show', $certification) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucune certification expirant bientôt</p>
            @endif
        </div>
    </div>
</div>
@endsection
