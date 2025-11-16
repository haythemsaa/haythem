@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-contract"></i> Nouveau Contrat</h2>
        <a href="{{ route('contracts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations Contrat</h5></div>
        <div class="card-body">
            <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">N° Contrat <span class="text-danger">*</span></label>
                        <input type="text" name="contract_number" class="form-control @error('contract_number') is-invalid @enderror" value="{{ old('contract_number') }}" required>
                        @error('contract_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de Contrat <span class="text-danger">*</span></label>
                        <select name="contract_type" class="form-select @error('contract_type') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="Maintenance" {{ old('contract_type') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="Leasing" {{ old('contract_type') == 'Leasing' ? 'selected' : '' }}>Leasing</option>
                            <option value="Assurance" {{ old('contract_type') == 'Assurance' ? 'selected' : '' }}>Assurance</option>
                            <option value="Service" {{ old('contract_type') == 'Service' ? 'selected' : '' }}>Service</option>
                            <option value="Autre" {{ old('contract_type') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('contract_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fournisseur <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name') }}" required>
                        @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Véhicule</label>
                        <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                            <option value="">Sélectionner un véhicule (optionnel)</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                        <label class="form-label">Coût Mensuel (DH) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="monthly_cost" class="form-control @error('monthly_cost') is-invalid @enderror" value="{{ old('monthly_cost') }}" required>
                        @error('monthly_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Coût Total (DH)</label>
                        <input type="number" step="0.01" name="total_cost" class="form-control @error('total_cost') is-invalid @enderror" value="{{ old('total_cost') }}">
                        @error('total_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Renouvellement Automatique</label>
                        <select name="auto_renewal" class="form-select @error('auto_renewal') is-invalid @enderror">
                            <option value="0" {{ old('auto_renewal') == '0' ? 'selected' : '' }}>Non</option>
                            <option value="1" {{ old('auto_renewal') == '1' ? 'selected' : '' }}>Oui</option>
                        </select>
                        @error('auto_renewal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Document Contrat</label>
                        <input type="file" name="contract_document" class="form-control @error('contract_document') is-invalid @enderror">
                        @error('contract_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
