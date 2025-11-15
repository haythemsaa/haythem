@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0"><i class="fas fa-plus-circle"></i> Nouvelle Intervention</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('interventions.index') }}">Interventions</a></li>
                    <li class="breadcrumb-item active">Nouvelle</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('interventions.store') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="vehicle_id" class="form-label">Véhicule <span class="text-danger">*</span></label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror"
                                id="vehicle_id" name="vehicle_id" required>
                            <option value="">Sélectionnez un véhicule</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_number }} - {{ $vehicle->brand?->name }} {{ $vehicle->vehicleModel?->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">Demandeur <span class="text-danger">*</span></label>
                        <select class="form-select @error('employee_id') is-invalid @enderror"
                                id="employee_id" name="employee_id" required>
                            <option value="">Sélectionnez un employé</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} ({{ $employee->employee_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Classification</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type', 'curative') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="urgency" class="form-label">Urgence <span class="text-danger">*</span></label>
                        <select class="form-select @error('urgency') is-invalid @enderror" id="urgency" name="urgency" required>
                            @foreach($urgencies as $urgency)
                                <option value="{{ $urgency }}" {{ old('urgency', 'normal') == $urgency ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $urgency)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('urgency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="severity" class="form-label">Sévérité</label>
                        <select class="form-select @error('severity') is-invalid @enderror" id="severity" name="severity">
                            <option value="">Sélectionnez</option>
                            @foreach($severities as $severity)
                                <option value="{{ $severity }}" {{ old('severity') == $severity ? 'selected' : '' }}>
                                    {{ ucfirst($severity) }}
                                </option>
                            @endforeach
                        </select>
                        @error('severity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="intervention_category_id" class="form-label">Catégorie</label>
                        <select class="form-select @error('intervention_category_id') is-invalid @enderror"
                                id="intervention_category_id" name="intervention_category_id">
                            <option value="">Sélectionnez</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('intervention_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('intervention_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="request_date" class="form-label">Date de Demande <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('request_date') is-invalid @enderror"
                               id="request_date" name="request_date"
                               value="{{ old('request_date', date('Y-m-d')) }}" required>
                        @error('request_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="mileage_at_request" class="form-label">Kilométrage</label>
                        <input type="number" class="form-control @error('mileage_at_request') is-invalid @enderror"
                               id="mileage_at_request" name="mileage_at_request"
                               value="{{ old('mileage_at_request') }}"
                               placeholder="Auto-rempli si vide">
                        <small class="text-muted">Laissez vide pour utiliser le kilométrage actuel du véhicule</small>
                        @error('mileage_at_request')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('interventions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
