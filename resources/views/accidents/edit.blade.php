@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Modifier l'Accident</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('accidents.update', $accident) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Vehicle -->
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label required">Véhicule</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner un véhicule</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $accident->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
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

                            <!-- Employee -->
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label required">Conducteur</label>
                                <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner un conducteur</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $accident->employee_id) == $employee->id ? 'selected' : '' }}>
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

                            <!-- Accident Date -->
                            <div class="col-md-6 mb-3">
                                <label for="accident_date" class="form-label required">Date de l'Accident</label>
                                <input type="date" name="accident_date" id="accident_date" class="form-control @error('accident_date') is-invalid @enderror" value="{{ old('accident_date', $accident->accident_date->format('Y-m-d')) }}" required>
                                @error('accident_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Accident Time -->
                            <div class="col-md-6 mb-3">
                                <label for="accident_time" class="form-label">Heure de l'Accident</label>
                                <input type="time" name="accident_time" id="accident_time" class="form-control @error('accident_time') is-invalid @enderror" value="{{ old('accident_time', $accident->accident_time ? \Carbon\Carbon::parse($accident->accident_time)->format('H:i') : '') }}">
                                @error('accident_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-12 mb-3">
                                <label for="location" class="form-label required">Lieu de l'Accident</label>
                                <textarea name="location" id="location" rows="2" class="form-control @error('location') is-invalid @enderror" required>{{ old('location', $accident->location) }}</textarea>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label required">Description de l'Accident</label>
                                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $accident->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Severity -->
                            <div class="col-md-4 mb-3">
                                <label for="severity" class="form-label required">Gravité</label>
                                <select name="severity" id="severity" class="form-select @error('severity') is-invalid @enderror" required>
                                    @foreach($severities as $key => $label)
                                        <option value="{{ $key }}" {{ old('severity', $accident->severity) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('severity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Injured Count -->
                            <div class="col-md-4 mb-3">
                                <label for="injured_count" class="form-label">Nombre de Blessés</label>
                                <input type="number" name="injured_count" id="injured_count" class="form-control @error('injured_count') is-invalid @enderror" value="{{ old('injured_count', $accident->injured_count) }}" min="0">
                                @error('injured_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fatalities Count -->
                            <div class="col-md-4 mb-3">
                                <label for="fatalities_count" class="form-label">Nombre de Décès</label>
                                <input type="number" name="fatalities_count" id="fatalities_count" class="form-control @error('fatalities_count') is-invalid @enderror" value="{{ old('fatalities_count', $accident->fatalities_count) }}" min="0">
                                @error('fatalities_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Material Damage -->
                            <div class="col-md-12 mb-3">
                                <label for="material_damage" class="form-label">Dégâts Matériels</label>
                                <textarea name="material_damage" id="material_damage" rows="3" class="form-control @error('material_damage') is-invalid @enderror">{{ old('material_damage', $accident->material_damage) }}</textarea>
                                @error('material_damage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Third Parties -->
                            <div class="col-md-12 mb-3">
                                <label for="third_parties" class="form-label">Tiers Impliqués</label>
                                <textarea name="third_parties" id="third_parties" rows="3" class="form-control @error('third_parties') is-invalid @enderror">{{ old('third_parties', $accident->third_parties) }}</textarea>
                                <small class="form-text text-muted">Informations sur les autres véhicules ou personnes impliqués</small>
                                @error('third_parties')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Police Report Number -->
                            <div class="col-md-6 mb-3">
                                <label for="police_report_number" class="form-label">N° Procès-Verbal de Police</label>
                                <input type="text" name="police_report_number" id="police_report_number" class="form-control @error('police_report_number') is-invalid @enderror" value="{{ old('police_report_number', $accident->police_report_number) }}">
                                @error('police_report_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Insurance Claim Number -->
                            <div class="col-md-6 mb-3">
                                <label for="insurance_claim_number" class="form-label">N° Déclaration Assurance</label>
                                <input type="text" name="insurance_claim_number" id="insurance_claim_number" class="form-control @error('insurance_claim_number') is-invalid @enderror" value="{{ old('insurance_claim_number', $accident->insurance_claim_number) }}">
                                @error('insurance_claim_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Estimated Cost -->
                            <div class="col-md-6 mb-3">
                                <label for="estimated_cost" class="form-label">Coût Estimé (DH)</label>
                                <input type="number" name="estimated_cost" id="estimated_cost" step="0.01" class="form-control @error('estimated_cost') is-invalid @enderror" value="{{ old('estimated_cost', $accident->estimated_cost) }}">
                                @error('estimated_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label required">Statut</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach($statuses as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', $accident->status) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('accidents.show', $accident) }}" class="btn btn-secondary">
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
