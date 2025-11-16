@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-handshake"></i> Locations</h2>
        @can('create_vehicles')
        <a href="{{ route('rentals.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle Location</a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3"><div class="card border-primary"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="text-muted mb-1">Total</h6><h3 class="mb-0">{{ $totalRentals }}</h3></div><div class="text-primary"><i class="fas fa-handshake fa-2x"></i></div></div></div></div></div>
        <div class="col-md-3"><div class="card border-success"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="text-muted mb-1">En Cours</h6><h3 class="mb-0">{{ $ongoingRentals }}</h3></div><div class="text-success"><i class="fas fa-car fa-2x"></i></div></div></div></div></div>
        <div class="col-md-3"><div class="card border-info"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="text-muted mb-1">Réservées</h6><h3 class="mb-0">{{ $reservedRentals }}</h3></div><div class="text-info"><i class="fas fa-calendar-check fa-2x"></i></div></div></div></div></div>
        <div class="col-md-3"><div class="card border-dark"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="text-muted mb-1">Revenu Total</h6><h3 class="mb-0">{{ number_format($totalRevenue, 0) }} DH</h3></div><div class="text-dark"><i class="fas fa-coins fa-2x"></i></div></div></div></div></div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-list"></i> Liste des Locations</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>N° Location</th><th>Client</th><th>Véhicule</th><th>Période</th><th>Tarif/Jour</th><th>Total</th><th>Statut</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($rentals as $rental)
                        <tr>
                            <td>{{ $rental->rental_number }}</td>
                            <td>{{ $rental->client_name }}</td>
                            <td>{{ $rental->vehicle->registration_number ?? '-' }}</td>
                            <td>{{ $rental->start_date->format('d/m/Y') }} - {{ $rental->end_date->format('d/m/Y') }}</td>
                            <td>{{ number_format($rental->daily_rate, 2) }} DH</td>
                            <td>{{ number_format($rental->total_cost, 2) }} DH</td>
                            <td><span class="badge bg-{{ $rental->status_color }}">{{ $rental->status_label }}</span></td>
                            <td>
                                <a href="{{ route('rentals.show', $rental) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                @if($rental->status === 'ongoing')
                                <form action="{{ route('rentals.complete', $rental) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                                </form>
                                @endif
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center">Aucune location trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $rentals->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
