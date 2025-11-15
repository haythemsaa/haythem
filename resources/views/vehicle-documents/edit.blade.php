@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-edit"></i> Modifier Document</h1>

    <form action="{{ route('vehicle-documents.update', $vehicleDocument) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Véhicule <span class="text-danger">*</span></label>
                        <select class="form-select" name="vehicle_id" required>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ $vehicleDocument->vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->registration_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Type Document <span class="text-danger">*</span></label>
                        <select class="form-select" name="document_type" required>
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}" {{ $vehicleDocument->document_type == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>N° Document</label>
                        <input type="text" class="form-control" name="document_number" value="{{ $vehicleDocument->document_number }}">
                    </div>
                    <div class="col-md-6">
                        <label>Autorité Emettrice</label>
                        <input type="text" class="form-control" name="issuing_authority" value="{{ $vehicleDocument->issuing_authority }}">
                    </div>
                    <div class="col-md-4">
                        <label>Date Emission</label>
                        <input type="date" class="form-control" name="issue_date" value="{{ $vehicleDocument->issue_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Date Expiration</label>
                        <input type="date" class="form-control" name="expiry_date" value="{{ $vehicleDocument->expiry_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Rappel (jours avant)</label>
                        <input type="number" class="form-control" name="reminder_days" value="{{ $vehicleDocument->reminder_days }}" min="1" max="365">
                    </div>
                    <div class="col-md-8">
                        <label>Fichier Document</label>
                        @if($vehicleDocument->file_path)
                            <div class="mb-2">
                                <small class="text-muted">Fichier actuel: {{ $vehicleDocument->file_name }}</small>
                            </div>
                        @endif
                        <input type="file" class="form-control" name="document_file" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG - Max 10MB - Laissez vide pour conserver l'actuel</small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_valid" {{ $vehicleDocument->is_valid ? 'checked' : '' }}>
                            <label class="form-check-label">Document Valide</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="2">{{ $vehicleDocument->notes }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('vehicle-documents.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
