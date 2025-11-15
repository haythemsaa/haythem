@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0"><i class="fas fa-gas-pump"></i> Gestion du Carburant</h1>
            <p class="text-muted">Suivi des ravitaillements et consommations</p>
        </div>
        <div class="col-md-6 text-end">
            @can('create_fuel')
                <a href="{{ route('fuel-consumptions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Ravitaillement
                </a>
                <a href="{{ route('fuel-consumptions.analytics') }}" class="btn btn-info">
                    <i class="fas fa-chart-line"></i> Analyses
                </a>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Total Dépensé</h6>
                    <h3 class="text-success">{{ number_format($totalSpent ?? 0, 2, ',', ' ') }} €</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Total Litres</h6>
                    <h3 class="text-primary">{{ number_format($totalLiters ?? 0, 2, ',', ' ') }} L</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Ravitaillements</h6>
                    <h3 class="text-warning">{{ $totalRefuelings ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="text-muted">Conso. Moyenne</h6>
                    <h3 class="text-info">{{ number_format($avgConsumption ?? 0, 2) }} L/100km</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light"><h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5></div>
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>Recherche</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Facture, lieu...">
                    </div>
                    <div class="col-md-2">
                        <label>Véhicule</label>
                        <select class="form-select" name="vehicle_id">
                            <option value="">Tous</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->registration_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Type Carburant</label>
                        <select class="form-select" name="fuel_type_id">
                            <option value="">Tous</option>
                            @foreach($fuelTypes as $ft)
                                <option value="{{ $ft->id }}" {{ request('fuel_type_id') == $ft->id ? 'selected' : '' }}>{{ $ft->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Date Début</label>
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label>Date Fin</label>
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        <a href="{{ route('fuel-consumptions.index') }}" class="btn btn-outline-secondary ms-2"><i class="fas fa-redo"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fuel Consumptions Table -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Ravitaillements <span class="badge bg-light text-dark">{{ $fuelConsumptions->total() }}</span></h5>
        </div>
        <div class="card-body p-0">
            @if($fuelConsumptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Véhicule</th>
                                <th>Carburant</th>
                                <th>Quantité</th>
                                <th>Prix Unit.</th>
                                <th>Total</th>
                                <th>Kilométrage</th>
                                <th>Consommation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fuelConsumptions as $fc)
                                <tr>
                                    <td>{{ $fc->refueling_date->format('d/m/Y') }}</td>
                                    <td><strong>{{ $fc->vehicle?->registration_number }}</strong></td>
                                    <td>{{ $fc->fuelType?->name }}</td>
                                    <td>{{ number_format($fc->quantity, 2) }} L</td>
                                    <td>{{ number_format($fc->unit_price, 3) }} €</td>
                                    <td><strong>{{ number_format($fc->total_amount, 2) }} €</strong></td>
                                    <td>{{ number_format($fc->mileage, 0, ',', ' ') }} km</td>
                                    <td>
                                        @if($fc->consumption_rate)
                                            <span class="badge bg-info">{{ number_format($fc->consumption_rate, 2) }} L/100km</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @can('view_fuel')
                                                <a href="{{ route('fuel-consumptions.show', $fc) }}" class="btn btn-outline-info" title="Voir"><i class="fas fa-eye"></i></a>
                                            @endcan
                                            @can('edit_fuel')
                                                <a href="{{ route('fuel-consumptions.edit', $fc) }}" class="btn btn-outline-primary" title="Modifier"><i class="fas fa-edit"></i></a>
                                            @endcan
                                            @can('delete_fuel')
                                                <form action="{{ route('fuel-consumptions.destroy', $fc) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Supprimer"><i class="fas fa-trash"></i></button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $fuelConsumptions->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-gas-pump fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Aucun ravitaillement</p>
                    @can('create_fuel')
                        <a href="{{ route('fuel-consumptions.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
