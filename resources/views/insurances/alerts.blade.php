@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell"></i> Alertes Assurances</h2>
        <a href="{{ route('insurances.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white"><h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Assurances Expirées ({{ $expiredInsurances->count() }})</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Véhicule</th><th>Compagnie</th><th>N° Police</th><th>Type</th><th>Date Fin</th><th>Jours Écoulés</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($expiredInsurances as $insurance)
                        <tr class="table-danger">
                            <td>{{ $insurance->vehicle->registration_number ?? '-' }}</td>
                            <td>{{ $insurance->insurance_company }}</td>
                            <td>{{ $insurance->policy_number }}</td>
                            <td>{{ $insurance->insurance_type }}</td>
                            <td>{{ $insurance->end_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-danger">{{ $insurance->end_date->diffInDays(now()) }} jours</span></td>
                            <td>
                                <a href="{{ route('insurances.show', $insurance) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('insurances.edit', $insurance) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-success">Aucune assurance expirée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-warning">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Expirent Bientôt - 30 jours ({{ $expiringSoonInsurances->count() }})</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Véhicule</th><th>Compagnie</th><th>N° Police</th><th>Type</th><th>Date Fin</th><th>Jours Restants</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($expiringSoonInsurances as $insurance)
                        <tr class="table-warning">
                            <td>{{ $insurance->vehicle->registration_number ?? '-' }}</td>
                            <td>{{ $insurance->insurance_company }}</td>
                            <td>{{ $insurance->policy_number }}</td>
                            <td>{{ $insurance->insurance_type }}</td>
                            <td>{{ $insurance->end_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-warning">{{ now()->diffInDays($insurance->end_date) }} jours</span></td>
                            <td>
                                <a href="{{ route('insurances.show', $insurance) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('insurances.edit', $insurance) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-success">Aucune assurance expirant bientôt</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
