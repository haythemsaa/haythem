@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8"><h1 class="h3"><i class="fas fa-gas-pump"></i> Détails Ravitaillement</h1></div>
        <div class="col-md-4 text-end">
            @can('edit_fuel')
                <a href="{{ route('fuel-consumptions.edit', $fuelConsumption) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Modifier</a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations Générales</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Véhicule</th><td><strong>{{ $fuelConsumption->vehicle?->registration_number }}</strong></td></tr>
                        <tr><th>Conducteur</th><td>{{ $fuelConsumption->employee?->full_name }}</td></tr>
                        <tr><th>Date</th><td>{{ $fuelConsumption->refueling_date->format('d/m/Y') }}</td></tr>
                        <tr><th>Type Carburant</th><td>{{ $fuelConsumption->fuelType?->name }}</td></tr>
                        <tr><th>Fournisseur</th><td>{{ $fuelConsumption->supplier?->name ?? 'N/A' }}</td></tr>
                        <tr><th>Lieu</th><td>{{ $fuelConsumption->location ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white"><h5 class="mb-0">Détails Ravitaillement</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Quantité</th><td><strong>{{ number_format($fuelConsumption->quantity, 2) }} L</strong></td></tr>
                        <tr><th>Prix Unitaire</th><td>{{ number_format($fuelConsumption->unit_price, 3) }} €/L</td></tr>
                        <tr><th>Total</th><td><strong class="text-success">{{ number_format($fuelConsumption->total_amount, 2) }} €</strong></td></tr>
                        <tr><th>Kilométrage</th><td>{{ number_format($fuelConsumption->mileage, 0, ',', ' ') }} km</td></tr>
                        <tr><th>Distance Parcourue</th><td>{{ number_format($fuelConsumption->distance_covered ?? 0, 0, ',', ' ') }} km</td></tr>
                        <tr><th>Consommation</th><td>
                            @if($fuelConsumption->consumption_rate)
                                <span class="badge bg-info">{{ number_format($fuelConsumption->consumption_rate, 2) }} L/100km</span>
                            @else
                                <span class="text-muted">Non calculée</span>
                            @endif
                        </td></tr>
                        <tr><th>Plein Complet</th><td>
                            @if($fuelConsumption->is_full_tank)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-secondary">Non</span>
                            @endif
                        </td></tr>
                        <tr><th>N° Facture</th><td>{{ $fuelConsumption->invoice_number ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        @if($fuelConsumption->notes)
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">Notes</h5></div>
                <div class="card-body"><p>{{ $fuelConsumption->notes }}</p></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
