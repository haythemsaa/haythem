@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shield-alt"></i> Nouvelle Assurance</h2>
        <a href="{{ route('insurances.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations Assurance</h5></div>
        <div class="card-body">
            <form action="{{ route('insurances.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                        <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un véhicule</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Compagnie d'Assurance <span class="text-danger">*</span></label>
                        <input type="text" name="insurance_company" class="form-control @error('insurance_company') is-invalid @enderror" value="{{ old('insurance_company') }}" required>
                        @error('insurance_company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">N° Police <span class="text-danger">*</span></label>
                        <input type="text" name="policy_number" class="form-control @error('policy_number') is-invalid @enderror" value="{{ old('policy_number') }}" required>
                        @error('policy_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="insurance_type" class="form-select @error('insurance_type') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="Responsabilité Civile" {{ old('insurance_type') == 'Responsabilité Civile' ? 'selected' : '' }}>Responsabilité Civile</option>
                            <option value="Tous Risques" {{ old('insurance_type') == 'Tous Risques' ? 'selected' : '' }}>Tous Risques</option>
                            <option value="Tiers Collision" {{ old('insurance_type') == 'Tiers Collision' ? 'selected' : '' }}>Tiers Collision</option>
                        </select>
                        @error('insurance_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de Couverture <span class="text-danger">*</span></label>
                        <select name="coverage_type" class="form-select @error('coverage_type') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="Basique" {{ old('coverage_type') == 'Basique' ? 'selected' : '' }}>Basique</option>
                            <option value="Complète" {{ old('coverage_type') == 'Complète' ? 'selected' : '' }}>Complète</option>
                            <option value="Premium" {{ old('coverage_type') == 'Premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        @error('coverage_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prime (DH) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="premium_amount" class="form-control @error('premium_amount') is-invalid @enderror" value="{{ old('premium_amount') }}" required>
                        @error('premium_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date Début <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date Fin <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Franchise (DH)</label>
                        <input type="number" step="0.01" name="deductible" class="form-control @error('deductible') is-invalid @enderror" value="{{ old('deductible') }}">
                        @error('deductible')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Document Police</label>
                        <input type="file" name="policy_document" class="form-control @error('policy_document') is-invalid @enderror">
                        @error('policy_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('insurances.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
