@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-contract"></i> Contrats</h2>
        @can('create_vehicles')
        <div>
            <a href="{{ route('contracts.alerts') }}" class="btn btn-warning me-2"><i class="fas fa-bell"></i> Alertes</a>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau Contrat</a>
        </div>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Total</h6><h3 class="mb-0">{{ $totalContracts }}</h3></div>
                        <div class="text-primary"><i class="fas fa-file-contract fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Actifs</h6><h3 class="mb-0">{{ $activeContracts }}</h3></div>
                        <div class="text-success"><i class="fas fa-check-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Expirent Bientôt</h6><h3 class="mb-0">{{ $expiringSoonContracts }}</h3></div>
                        <div class="text-warning"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Coût Mensuel</h6><h3 class="mb-0">{{ number_format($totalMonthlyCost, 0) }} DH</h3></div>
                        <div class="text-dark"><i class="fas fa-coins fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-list"></i> Liste des Contrats</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>N° Contrat</th><th>Type</th><th>Fournisseur</th><th>Véhicule</th><th>Date Fin</th><th>Coût Mensuel</th><th>Statut</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                        <tr>
                            <td>{{ $contract->contract_number }}</td>
                            <td>{{ $contract->contract_type }}</td>
                            <td>{{ $contract->supplier_name }}</td>
                            <td>{{ $contract->vehicle->registration_number ?? '-' }}</td>
                            <td>{{ $contract->end_date->format('d/m/Y') }}</td>
                            <td>{{ number_format($contract->monthly_cost, 2) }} DH</td>
                            <td><span class="badge bg-{{ $contract->status_color }}">{{ $contract->status_label }}</span></td>
                            <td>
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center">Aucun contrat trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $contracts->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
