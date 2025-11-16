@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell"></i> Alertes Contrats</h2>
        <a href="{{ route('contracts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white"><h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Contrats Expirés ({{ $expiredContracts->count() }})</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>N° Contrat</th><th>Type</th><th>Fournisseur</th><th>Véhicule</th><th>Date Fin</th><th>Jours Écoulés</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($expiredContracts as $contract)
                        <tr class="table-danger">
                            <td>{{ $contract->contract_number }}</td>
                            <td>{{ $contract->contract_type }}</td>
                            <td>{{ $contract->supplier_name }}</td>
                            <td>{{ $contract->vehicle->registration_number ?? '-' }}</td>
                            <td>{{ $contract->end_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-danger">{{ $contract->end_date->diffInDays(now()) }} jours</span></td>
                            <td>
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-success">Aucun contrat expiré</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-warning">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Expirent Bientôt - 30 jours ({{ $expiringSoonContracts->count() }})</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>N° Contrat</th><th>Type</th><th>Fournisseur</th><th>Véhicule</th><th>Date Fin</th><th>Jours Restants</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($expiringSoonContracts as $contract)
                        <tr class="table-warning">
                            <td>{{ $contract->contract_number }}</td>
                            <td>{{ $contract->contract_type }}</td>
                            <td>{{ $contract->supplier_name }}</td>
                            <td>{{ $contract->vehicle->registration_number ?? '-' }}</td>
                            <td>{{ $contract->end_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-warning">{{ now()->diffInDays($contract->end_date) }} jours</span></td>
                            <td>
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-success">Aucun contrat expirant bientôt</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
