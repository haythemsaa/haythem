@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tire"></i> Nouveau Pneumatique</h2>
        <a href="{{ route('tires.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tires.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Tire Information -->
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-info-circle"></i> Informations Pneu</h5>

                        <div class="mb-3">
                            <label for="brand" class="form-label">Marque *</label>
                            <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                   id="brand" name="brand" value="{{ old('brand') }}" required>
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="model" class="form-label">Modèle *</label>
                            <input type="text" class="form-control @error('model') is-invalid @enderror"
                                   id="model" name="model" value="{{ old('model') }}" required>
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="size" class="form-label">Dimension *</label>
                            <input type="text" class="form-control @error('size') is-invalid @enderror"
                                   id="size" name="size" value="{{ old('size') }}"
                                   placeholder="ex: 205/55R16" required>
                            @error('size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tire_type" class="form-label">Type *</label>
                            <select class="form-select @error('tire_type') is-invalid @enderror"
                                    id="tire_type" name="tire_type" required>
                                <option value="">Sélectionner un type</option>
                                @foreach($tireTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('tire_type') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tire_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_id" class="form-label">Site *</label>
                            <select class="form-select @error('site_id') is-invalid @enderror"
                                    id="site_id" name="site_id" required>
                                <option value="">Sélectionner un site</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Installation & Status -->
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-cog"></i> Installation & Statut</h5>

                        <div class="mb-3">
                            <label for="vehicle_id" class="form-label">Véhicule</label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror"
                                    id="vehicle_id" name="vehicle_id">
                                <option value="">Aucun (Stock)</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">Position *</label>
                            <select class="form-select @error('position') is-invalid @enderror"
                                    id="position" name="position" required>
                                @foreach($tirePositions as $key => $label)
                                    <option value="{{ $key }}" {{ old('position') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                @foreach($tireStatuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', 'in_stock') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Date d'Achat *</label>
                            <input type="date" class="form-control @error('purchase_date') is-invalid @enderror"
                                   id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="installation_date" class="form-label">Date d'Installation</label>
                            <input type="date" class="form-control @error('installation_date') is-invalid @enderror"
                                   id="installation_date" name="installation_date" value="{{ old('installation_date') }}">
                            @error('installation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchase_cost" class="form-label">Coût d'Achat (DH) *</label>
                            <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror"
                                   id="purchase_cost" name="purchase_cost" value="{{ old('purchase_cost') }}" required>
                            @error('purchase_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Tread Depth & Mileage -->
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-ruler-vertical"></i> Profondeur de Sculpture</h5>

                        <div class="mb-3">
                            <label for="initial_tread_depth" class="form-label">Profondeur Initiale (mm)</label>
                            <input type="number" step="0.1" class="form-control @error('initial_tread_depth') is-invalid @enderror"
                                   id="initial_tread_depth" name="initial_tread_depth"
                                   value="{{ old('initial_tread_depth', '8.0') }}"
                                   placeholder="8.0">
                            @error('initial_tread_depth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Par défaut: 8.0 mm pour un pneu neuf</small>
                        </div>

                        <div class="mb-3">
                            <label for="current_tread_depth" class="form-label">Profondeur Actuelle (mm)</label>
                            <input type="number" step="0.1" class="form-control @error('current_tread_depth') is-invalid @enderror"
                                   id="current_tread_depth" name="current_tread_depth"
                                   value="{{ old('current_tread_depth') }}"
                                   placeholder="8.0">
                            @error('current_tread_depth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum légal: 1.6 mm</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-tachometer-alt"></i> Kilométrage</h5>

                        <div class="mb-3">
                            <label for="mileage_at_installation" class="form-label">Km à l'Installation</label>
                            <input type="number" class="form-control @error('mileage_at_installation') is-invalid @enderror"
                                   id="mileage_at_installation" name="mileage_at_installation"
                                   value="{{ old('mileage_at_installation') }}">
                            @error('mileage_at_installation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="current_mileage" class="form-label">Km Actuel</label>
                            <input type="number" class="form-control @error('current_mileage') is-invalid @enderror"
                                   id="current_mileage" name="current_mileage" value="{{ old('current_mileage') }}">
                            @error('current_mileage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="fas fa-comment"></i> Remarques</h5>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('tires.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
