@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell"></i> Alertes Permis de Conduire</h2>
        <a href="{{ route('driving-licenses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- Expired Licenses -->
    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-times-circle"></i> Permis Expirés ({{ $expiredLicenses->count() }})</h5>
        </div>
        <div class="card-body">
            @if($expiredLicenses->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>N° Permis</th>
                            <th>Date Expiration</th>
                            <th>Jours Dépassés</th>
                            <th>Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiredLicenses as $license)
                        <tr>
                            <td>{{ $license->employee->first_name }} {{ $license->employee->last_name }}</td>
                            <td>{{ $license->license_number }}</td>
                            <td>{{ $license->expiry_date->format('d/m/Y') }}</td>
                            <td class="text-danger">
                                <strong>{{ $license->expiry_date->diffInDays(now()) }} jours</strong>
                            </td>
                            <td><span class="badge bg-{{ $license->points_color }}">{{ $license->points }} pts</span></td>
                            <td>
                                <a href="{{ route('driving-licenses.show', $license) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucun permis expiré</p>
            @endif
        </div>
    </div>

    <!-- Expiring Soon Licenses -->
    <div class="card border-warning mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Permis Expirant Bientôt ({{ $expiringSoonLicenses->count() }})</h5>
        </div>
        <div class="card-body">
            @if($expiringSoonLicenses->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>N° Permis</th>
                            <th>Date Expiration</th>
                            <th>Jours Restants</th>
                            <th>Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringSoonLicenses as $license)
                        <tr>
                            <td>{{ $license->employee->first_name }} {{ $license->employee->last_name }}</td>
                            <td>{{ $license->license_number }}</td>
                            <td>{{ $license->expiry_date->format('d/m/Y') }}</td>
                            <td class="text-warning">
                                <strong>{{ $license->days_until_expiry }} jours</strong>
                            </td>
                            <td><span class="badge bg-{{ $license->points_color }}">{{ $license->points }} pts</span></td>
                            <td>
                                <a href="{{ route('driving-licenses.show', $license) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucun permis expirant bientôt</p>
            @endif
        </div>
    </div>

    <!-- Low Points Licenses -->
    <div class="card border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-minus-circle"></i> Permis à Points Faibles ({{ $lowPointsLicenses->count() }})</h5>
        </div>
        <div class="card-body">
            @if($lowPointsLicenses->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>N° Permis</th>
                            <th>Points Restants</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowPointsLicenses as $license)
                        <tr>
                            <td>{{ $license->employee->first_name }} {{ $license->employee->last_name }}</td>
                            <td>{{ $license->license_number }}</td>
                            <td>
                                <span class="badge bg-{{ $license->points_color }} fs-6">
                                    {{ $license->points }} / 12 points
                                </span>
                            </td>
                            <td><span class="badge bg-{{ $license->status_color }}">{{ $license->status_label }}</span></td>
                            <td>
                                <a href="{{ route('driving-licenses.show', $license) }}" class="btn btn-sm btn-info">Voir</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucun permis à points faibles</p>
            @endif
        </div>
    </div>
</div>
@endsection
