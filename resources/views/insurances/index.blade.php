@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shield-alt"></i> Assurances</h2>
        @can('create_vehicles')
        <div>
            <a href="{{ route('insurances.alerts') }}" class="btn btn-warning me-2"><i class="fas fa-bell"></i> Alertes</a>
            <a href="{{ route('insurances.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle Assurance</a>
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
                        <div>
                            <h6 class="text-muted mb-1">Total</h6>
                            <h3 class="mb-0">{{ $totalInsurances }}</h3>
                        </div>
                        <div class="text-primary"><i class="fas fa-shield-alt fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Actives</h6>
                            <h3 class="mb-0">{{ $activeInsurances }}</h3>
                        </div>
                        <div class="text-success"><i class="fas fa-check-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Expirent Bientôt</h6>
                            <h3 class="mb-0">{{ $expiringSoonInsurances }}</h3>
                        </div>
                        <div class="text-warning"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Primes Totales</h6>
                            <h3 class="mb-0">{{ number_format($totalPremiums, 0) }} DH</h3>
                        </div>
                        <div class="text-dark"><i class="fas fa-coins fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-list"></i> Liste des Assurances</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Véhicule</th>
                            <th>Compagnie</th>
                            <th>N° Police</th>
                            <th>Type</th>
                            <th>Date Fin</th>
                            <th>Prime</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($insurances as $insurance)
                        <tr>
                            <td>{{ $insurance->vehicle->registration_number ?? '-' }}</td>
                            <td>{{ $insurance->insurance_company }}</td>
                            <td>{{ $insurance->policy_number }}</td>
                            <td>{{ $insurance->insurance_type }}</td>
                            <td>{{ $insurance->end_date->format('d/m/Y') }}</td>
                            <td>{{ number_format($insurance->premium_amount, 2) }} DH</td>
                            <td><span class="badge bg-{{ $insurance->status_color }}">{{ $insurance->status_label }}</span></td>
                            <td>
                                <a href="{{ route('insurances.show', $insurance) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('insurances.edit', $insurance) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center">Aucune assurance trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $insurances->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
