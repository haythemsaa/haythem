@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Modifier l'Infraction</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('traffic-violations.update', $trafficViolation) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Employee -->
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label required">Conducteur</label>
                                <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner un conducteur</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $trafficViolation->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                            @if($employee->employee_code)
                                                ({{ $employee->employee_code }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Vehicle -->
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label">Véhicule</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                                    <option value="">Sélectionner un véhicule (optionnel)</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $trafficViolation->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->registration_number }}
                                            @if($vehicle->internal_code)
                                                ({{ $vehicle->internal_code }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Violation Date -->
                            <div class="col-md-6 mb-3">
                                <label for="violation_date" class="form-label required">Date de l'Infraction</label>
                                <input type="date" name="violation_date" id="violation_date" class="form-control @error('violation_date') is-invalid @enderror" value="{{ old('violation_date', $trafficViolation->violation_date->format('Y-m-d')) }}" required>
                                @error('violation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Violation Type -->
                            <div class="col-md-6 mb-3">
                                <label for="violation_type" class="form-label required">Type d'Infraction</label>
                                <select name="violation_type" id="violation_type" class="form-select @error('violation_type') is-invalid @enderror" required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach($violationTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('violation_type', $trafficViolation->violation_type) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('violation_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-12 mb-3">
                                <label for="location" class="form-label">Lieu de l'Infraction</label>
                                <textarea name="location" id="location" rows="2" class="form-control @error('location') is-invalid @enderror">{{ old('location', $trafficViolation->location) }}</textarea>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fine Amount -->
                            <div class="col-md-4 mb-3">
                                <label for="fine_amount" class="form-label">Montant de l'Amende (DH)</label>
                                <input type="number" name="fine_amount" id="fine_amount" step="0.01" class="form-control @error('fine_amount') is-invalid @enderror" value="{{ old('fine_amount', $trafficViolation->fine_amount) }}">
                                @error('fine_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Points Deducted -->
                            <div class="col-md-4 mb-3">
                                <label for="points_deducted" class="form-label">Points Retirés</label>
                                <input type="number" name="points_deducted" id="points_deducted" class="form-control @error('points_deducted') is-invalid @enderror" value="{{ old('points_deducted', $trafficViolation->points_deducted) }}" min="0" max="30">
                                @error('points_deducted')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Additional Costs -->
                            <div class="col-md-4 mb-3">
                                <label for="additional_costs" class="form-label">Frais Additionnels (DH)</label>
                                <input type="number" name="additional_costs" id="additional_costs" step="0.01" class="form-control @error('additional_costs') is-invalid @enderror" value="{{ old('additional_costs', $trafficViolation->additional_costs) }}">
                                @error('additional_costs')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Consequences -->
                            <div class="col-md-12 mb-3">
                                <label for="consequences" class="form-label">Conséquences</label>
                                <textarea name="consequences" id="consequences" rows="3" class="form-control @error('consequences') is-invalid @enderror" placeholder="Suspension de permis, immobilisation du véhicule, etc.">{{ old('consequences', $trafficViolation->consequences) }}</textarea>
                                @error('consequences')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Attachment -->
                            @if($trafficViolation->attachment)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Pièce Jointe Actuelle</label>
                                <div class="d-flex align-items-center">
                                    <a href="{{ Storage::url($trafficViolation->attachment) }}" target="_blank" class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-eye"></i> Voir le fichier
                                    </a>
                                    <small class="text-muted">{{ basename($trafficViolation->attachment) }}</small>
                                </div>
                            </div>
                            @endif

                            <!-- New Attachment -->
                            <div class="col-md-12 mb-3">
                                <label for="attachment" class="form-label">{{ $trafficViolation->attachment ? 'Remplacer la Pièce Jointe' : 'Pièce Jointe' }}</label>
                                <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">Formats acceptés: PDF, JPG, PNG (Max: 10 MB)</small>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('traffic-violations.show', $trafficViolation) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à Jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.required::after {
    content: " *";
    color: red;
}
</style>
@endsection
