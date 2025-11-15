@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-edit"></i> Modifier Ravitaillement</h1>

    <form action="{{ route('fuel-consumptions.update', $fuelConsumption) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Véhicule <span class="text-danger">*</span></label>
                        <select class="form-select" name="vehicle_id" required>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ $fuelConsumption->vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->registration_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Conducteur <span class="text-danger">*</span></label>
                        <select class="form-select" name="employee_id" required>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}" {{ $fuelConsumption->employee_id == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Type Carburant <span class="text-danger">*</span></label>
                        <select class="form-select" name="fuel_type_id" required>
                            @foreach($fuelTypes as $ft)
                                <option value="{{ $ft->id }}" {{ $fuelConsumption->fuel_type_id == $ft->id ? 'selected' : '' }}>{{ $ft->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Fournisseur</label>
                        <select class="form-select" name="supplier_id">
                            <option value="">-</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ $fuelConsumption->supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="refueling_date" value="{{ $fuelConsumption->refueling_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label>Quantité (L) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="quantity" value="{{ $fuelConsumption->quantity }}" required>
                    </div>
                    <div class="col-md-3">
                        <label>Prix Unitaire (€) <span class="text-danger">*</span></label>
                        <input type="number" step="0.001" class="form-control" name="unit_price" value="{{ $fuelConsumption->unit_price }}" required>
                    </div>
                    <div class="col-md-3">
                        <label>Kilométrage <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="mileage" value="{{ $fuelConsumption->mileage }}" required>
                    </div>
                    <div class="col-md-3">
                        <label>KM Précédent</label>
                        <input type="number" class="form-control" name="previous_mileage" value="{{ $fuelConsumption->previous_mileage }}">
                    </div>
                    <div class="col-md-3">
                        <label>N° Facture</label>
                        <input type="text" class="form-control" name="invoice_number" value="{{ $fuelConsumption->invoice_number }}">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_full_tank" {{ $fuelConsumption->is_full_tank ? 'checked' : '' }}>
                            <label class="form-check-label">Plein complet</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Lieu</label>
                        <input type="text" class="form-control" name="location" value="{{ $fuelConsumption->location }}">
                    </div>
                    <div class="col-md-12">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="2">{{ $fuelConsumption->notes }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('fuel-consumptions.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
