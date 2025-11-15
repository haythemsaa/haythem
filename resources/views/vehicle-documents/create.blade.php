@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-file-alt"></i> Nouveau Document</h1>

    <form action="{{ route('vehicle-documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Véhicule <span class="text-danger">*</span></label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" name="vehicle_id" required>
                            <option value="">Sélectionnez</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ old('vehicle_id', $selectedVehicleId) == $v->id ? 'selected' : '' }}>
                                    {{ $v->registration_number }} - {{ $v->brand?->name }} {{ $v->vehicleModel?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label>Type Document <span class="text-danger">*</span></label>
                        <select class="form-select @error('document_type') is-invalid @enderror" name="document_type" required>
                            <option value="">Sélectionnez</option>
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('document_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label>N° Document</label>
                        <input type="text" class="form-control" name="document_number" value="{{ old('document_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label>Autorité Emettrice</label>
                        <input type="text" class="form-control" name="issuing_authority" value="{{ old('issuing_authority') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Date Emission</label>
                        <input type="date" class="form-control" name="issue_date" value="{{ old('issue_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Date Expiration</label>
                        <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" name="expiry_date" value="{{ old('expiry_date') }}">
                        @error('expiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label>Rappel (jours avant)</label>
                        <input type="number" class="form-control" name="reminder_days" value="{{ old('reminder_days', 30) }}" min="1" max="365">
                    </div>
                    <div class="col-md-8">
                        <label>Fichier Document</label>
                        <input type="file" class="form-control @error('document_file') is-invalid @enderror" name="document_file" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG - Max 10MB</small>
                        @error('document_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_valid" {{ old('is_valid', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Document Valide</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('vehicle-documents.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
        </div>
    </form>
</div>
@endsection
