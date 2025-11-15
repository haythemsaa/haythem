@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0"><i class="fas fa-edit"></i> Modifier Véhicule</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">Véhicules</a></li>
                    <li class="breadcrumb-item active">{{ $vehicle->registration_number }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Informations Générales -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="registration_number" class="form-label">
                            Immatriculation <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror"
                               id="registration_number" name="registration_number"
                               value="{{ old('registration_number', $vehicle->registration_number) }}" required>
                        @error('registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="internal_code" class="form-label">Code Interne</label>
                        <input type="text" class="form-control @error('internal_code') is-invalid @enderror"
                               id="internal_code" name="internal_code"
                               value="{{ old('internal_code', $vehicle->internal_code) }}">
                        @error('internal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="fleet_number" class="form-label">Numéro de Flotte</label>
                        <input type="text" class="form-control @error('fleet_number') is-invalid @enderror"
                               id="fleet_number" name="fleet_number"
                               value="{{ old('fleet_number', $vehicle->fleet_number) }}">
                        @error('fleet_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="vin" class="form-label">
                            Numéro de Châssis (VIN) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('vin') is-invalid @enderror"
                               id="vin" name="vin"
                               value="{{ old('vin', $vehicle->vin) }}" required maxlength="17">
                        @error('vin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="brand_id" class="form-label">
                            Marque <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('brand_id') is-invalid @enderror"
                                id="brand_id" name="brand_id" required>
                            <option value="">Sélectionnez une marque</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id', $vehicle->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="model_id" class="form-label">
                            Modèle <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('model_id') is-invalid @enderror"
                                id="model_id" name="model_id" required>
                            <option value="">Chargement...</option>
                        </select>
                        @error('model_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Caractéristiques Techniques -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Caractéristiques Techniques</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">
                            Catégorie <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                            <option value="">Sélectionnez</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $vehicle->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="year" class="form-label">Année</label>
                        <input type="number" class="form-control @error('year') is-invalid @enderror"
                               id="year" name="year" min="1900" max="2100"
                               value="{{ old('year', $vehicle->year) }}">
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="energy_type" class="form-label">Type d'Énergie</label>
                        <select class="form-select @error('energy_type') is-invalid @enderror"
                                id="energy_type" name="energy_type">
                            <option value="">Sélectionnez</option>
                            @foreach($energyTypes as $type)
                                <option value="{{ $type }}"
                                    {{ old('energy_type', $vehicle->energy_type) == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('energy_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="fuel_type_id" class="form-label">Type de Carburant</label>
                        <select class="form-select @error('fuel_type_id') is-invalid @enderror"
                                id="fuel_type_id" name="fuel_type_id">
                            <option value="">Sélectionnez</option>
                            @foreach($fuelTypes as $fuelType)
                                <option value="{{ $fuelType->id }}"
                                    {{ old('fuel_type_id', $vehicle->fuel_type_id) == $fuelType->id ? 'selected' : '' }}>
                                    {{ $fuelType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fuel_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="tank_capacity" class="form-label">Capacité Réservoir (L)</label>
                        <input type="number" step="0.01" class="form-control @error('tank_capacity') is-invalid @enderror"
                               id="tank_capacity" name="tank_capacity"
                               value="{{ old('tank_capacity', $vehicle->tank_capacity) }}">
                        @error('tank_capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="engine_power" class="form-label">Puissance (CV)</label>
                        <input type="number" class="form-control @error('engine_power') is-invalid @enderror"
                               id="engine_power" name="engine_power"
                               value="{{ old('engine_power', $vehicle->engine_power) }}">
                        @error('engine_power')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="seats" class="form-label">Places</label>
                        <input type="number" class="form-control @error('seats') is-invalid @enderror"
                               id="seats" name="seats"
                               value="{{ old('seats', $vehicle->seats) }}">
                        @error('seats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="doors" class="form-label">Portes</label>
                        <input type="number" class="form-control @error('doors') is-invalid @enderror"
                               id="doors" name="doors"
                               value="{{ old('doors', $vehicle->doors) }}">
                        @error('doors')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="color" class="form-label">Couleur</label>
                        <input type="text" class="form-control @error('color') is-invalid @enderror"
                               id="color" name="color"
                               value="{{ old('color', $vehicle->color) }}">
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Acquisition -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Acquisition</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="acquisition_mode_id" class="form-label">Mode d'Acquisition</label>
                        <select class="form-select @error('acquisition_mode_id') is-invalid @enderror"
                                id="acquisition_mode_id" name="acquisition_mode_id">
                            <option value="">Sélectionnez</option>
                            @foreach($acquisitionModes as $mode)
                                <option value="{{ $mode->id }}"
                                    {{ old('acquisition_mode_id', $vehicle->acquisition_mode_id) == $mode->id ? 'selected' : '' }}>
                                    {{ $mode->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('acquisition_mode_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="purchase_date" class="form-label">Date d'Achat</label>
                        <input type="date" class="form-control @error('purchase_date') is-invalid @enderror"
                               id="purchase_date" name="purchase_date"
                               value="{{ old('purchase_date', $vehicle->purchase_date?->format('Y-m-d')) }}">
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="purchase_price" class="form-label">Prix d'Achat (€)</label>
                        <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror"
                               id="purchase_price" name="purchase_price"
                               value="{{ old('purchase_price', $vehicle->purchase_price) }}">
                        @error('purchase_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Affectation et Kilométrage -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Affectation et Kilométrage</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="site_id" class="form-label">Site</label>
                        <select class="form-select @error('site_id') is-invalid @enderror"
                                id="site_id" name="site_id">
                            <option value="">Sélectionnez</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}"
                                    {{ old('site_id', $vehicle->site_id) == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('site_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="parc_id" class="form-label">Parc</label>
                        <select class="form-select @error('parc_id') is-invalid @enderror"
                                id="parc_id" name="parc_id">
                            <option value="">Sélectionnez</option>
                            @foreach($parcs as $parc)
                                <option value="{{ $parc->id }}"
                                    {{ old('parc_id', $vehicle->parc_id) == $parc->id ? 'selected' : '' }}>
                                    {{ $parc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parc_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status">
                            <option value="disponible" {{ old('status', $vehicle->status) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="en_mission" {{ old('status', $vehicle->status) == 'en_mission' ? 'selected' : '' }}>En Mission</option>
                            <option value="en_maintenance" {{ old('status', $vehicle->status) == 'en_maintenance' ? 'selected' : '' }}>En Maintenance</option>
                            <option value="en_panne" {{ old('status', $vehicle->status) == 'en_panne' ? 'selected' : '' }}>En Panne</option>
                            <option value="hors_service" {{ old('status', $vehicle->status) == 'hors_service' ? 'selected' : '' }}>Hors Service</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="current_mileage" class="form-label">Kilométrage Actuel (km)</label>
                        <input type="number" class="form-control @error('current_mileage') is-invalid @enderror"
                               id="current_mileage" name="current_mileage"
                               value="{{ old('current_mileage', $vehicle->current_mileage) }}">
                        @error('current_mileage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="mileage_acquisition" class="form-label">Kilométrage à l'Acquisition (km)</label>
                        <input type="number" class="form-control @error('mileage_acquisition') is-invalid @enderror"
                               id="mileage_acquisition" name="mileage_acquisition"
                               value="{{ old('mileage_acquisition', $vehicle->mileage_acquisition) }}">
                        @error('mileage_acquisition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const currentModelId = {{ old('model_id', $vehicle->model_id) }};
    const currentBrandId = {{ old('brand_id', $vehicle->brand_id) }};

    // Load models on page load
    if (currentBrandId) {
        loadModels(currentBrandId, currentModelId);
    }

    // Dynamic model loading based on brand selection
    document.getElementById('brand_id').addEventListener('change', function() {
        const brandId = this.value;
        loadModels(brandId);
    });

    function loadModels(brandId, selectedModelId = null) {
        const modelSelect = document.getElementById('model_id');

        if (!brandId) {
            modelSelect.innerHTML = '<option value="">Sélectionnez d\'abord une marque</option>';
            return;
        }

        // Clear current options
        modelSelect.innerHTML = '<option value="">Chargement...</option>';

        fetch(`/api/models-by-brand/${brandId}`)
            .then(response => response.json())
            .then(data => {
                modelSelect.innerHTML = '<option value="">Sélectionnez un modèle</option>';
                data.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.name;
                    if (selectedModelId && model.id == selectedModelId) {
                        option.selected = true;
                    }
                    modelSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                modelSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });
    }
</script>
@endpush
