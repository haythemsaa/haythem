@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-gas-pump"></i> Nouveau Ravitaillement</h1>

    <form action="{{ route('fuel-consumptions.store') }}" method="POST">
        @csrf
        <div class="card mb-4">
            <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations Ravitaillement</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Véhicule <span class="text-danger">*</span></label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" name="vehicle_id" id="vehicle_id" required>
                            <option value="">Sélectionnez</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" data-mileage="{{ $v->current_mileage }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->registration_number }} - {{ $v->brand?->name }} {{ $v->vehicleModel?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label>Conducteur <span class="text-danger">*</span></label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                            <option value="">Sélectionnez</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label>Type Carburant <span class="text-danger">*</span></label>
                        <select class="form-select @error('fuel_type_id') is-invalid @enderror" name="fuel_type_id" id="fuel_type_id" required>
                            <option value="">Sélectionnez</option>
                            @foreach($fuelTypes as $ft)
                                <option value="{{ $ft->id }}" data-price="{{ $ft->price_per_unit }}" {{ old('fuel_type_id') == $ft->id ? 'selected' : '' }}>{{ $ft->name }}</option>
                            @endforeach
                        </select>
                        @error('fuel_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label>Fournisseur</label>
                        <select class="form-select" name="supplier_id">
                            <option value="">-</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('refueling_date') is-invalid @enderror" name="refueling_date" value="{{ old('refueling_date', date('Y-m-d')) }}" required>
                        @error('refueling_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label>Quantité (L) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('quantity') is-invalid @enderror" name="quantity" id="quantity" value="{{ old('quantity') }}" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label>Prix Unitaire (€) <span class="text-danger">*</span></label>
                        <input type="number" step="0.001" class="form-control @error('unit_price') is-invalid @enderror" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" required>
                        @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label>Total (€)</label>
                        <input type="number" step="0.01" class="form-control" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Kilométrage <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('mileage') is-invalid @enderror" name="mileage" id="mileage" value="{{ old('mileage') }}" required>
                        @error('mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label>KM Précédent</label>
                        <input type="number" class="form-control" name="previous_mileage" id="previous_mileage" value="{{ old('previous_mileage') }}">
                    </div>
                    <div class="col-md-3">
                        <label>N° Facture</label>
                        <input type="text" class="form-control" name="invoice_number" value="{{ old('invoice_number') }}">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_full_tank" id="is_full_tank" {{ old('is_full_tank') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_full_tank">Plein complet</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Lieu</label>
                        <input type="text" class="form-control" name="location" value="{{ old('location') }}">
                    </div>
                    <div class="col-md-12">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('fuel-consumptions.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-calculate total
document.getElementById('quantity').addEventListener('input', calculateTotal);
document.getElementById('unit_price').addEventListener('input', calculateTotal);

function calculateTotal() {
    const qty = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(document.getElementById('unit_price').value) || 0;
    document.getElementById('total_amount').value = (qty * price).toFixed(2);
}

// Auto-fill price when fuel type changes
document.getElementById('fuel_type_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    if (price) {
        document.getElementById('unit_price').value = price;
        calculateTotal();
    }
});

// Auto-fill mileage when vehicle changes
document.getElementById('vehicle_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const mileage = selectedOption.getAttribute('data-mileage');
    if (mileage) {
        document.getElementById('mileage').value = mileage;
    }
});
</script>
@endpush
@endsection
