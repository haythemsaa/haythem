@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-sync"></i> Rotation de Pneumatique</h2>
        <a href="{{ route('tires.show', $tire) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-sync"></i> Effectuer une Rotation</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tires.store-rotation', $tire) }}" method="POST">
                        @csrf

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            La rotation des pneus prolonge leur durée de vie et assure une usure uniforme.
                            Il est recommandé d'effectuer une rotation tous les 10 000 km.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rotation_date" class="form-label">Date de Rotation *</label>
                                    <input type="date" class="form-control @error('rotation_date') is-invalid @enderror"
                                           id="rotation_date" name="rotation_date"
                                           value="{{ old('rotation_date', date('Y-m-d')) }}" required>
                                    @error('rotation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="from_position" class="form-label">Position Actuelle</label>
                                    <input type="text" class="form-control" value="{{ $tire->position_label }}" disabled>
                                </div>

                                <div class="mb-3">
                                    <label for="to_position" class="form-label">Nouvelle Position *</label>
                                    <select class="form-select @error('to_position') is-invalid @enderror"
                                            id="to_position" name="to_position" required>
                                        <option value="">Sélectionner une position</option>
                                        @foreach($tirePositions as $key => $label)
                                            @if($key !== $tire->position)
                                            <option value="{{ $key }}" {{ old('to_position') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('to_position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mileage" class="form-label">Kilométrage au Moment de la Rotation *</label>
                                    <input type="number" class="form-control @error('mileage') is-invalid @enderror"
                                           id="mileage" name="mileage"
                                           value="{{ old('mileage', $tire->current_mileage) }}" required>
                                    @error('mileage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tread_depth_before" class="form-label">Profondeur Avant Rotation</label>
                                    <input type="text" class="form-control"
                                           value="{{ number_format($tire->current_tread_depth, 1) }} mm" disabled>
                                </div>

                                <div class="mb-3">
                                    <label for="tread_depth_after" class="form-label">Profondeur Après Rotation (mm) *</label>
                                    <input type="number" step="0.1" class="form-control @error('tread_depth_after') is-invalid @enderror"
                                           id="tread_depth_after" name="tread_depth_after"
                                           value="{{ old('tread_depth_after', $tire->current_tread_depth) }}" required>
                                    @error('tread_depth_after')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Mesurez la profondeur après la rotation</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="technician" class="form-label">Technicien</label>
                            <input type="text" class="form-control @error('technician') is-invalid @enderror"
                                   id="technician" name="technician" value="{{ old('technician') }}"
                                   placeholder="Nom du technicien qui a effectué la rotation">
                            @error('technician')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Remarques</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Observations, anomalies détectées, etc.">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('tires.show', $tire) }}" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sync"></i> Effectuer la Rotation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Tire Information -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-tire"></i> Informations Pneu</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Code:</dt>
                        <dd class="col-sm-7"><strong>{{ $tire->tire_code }}</strong></dd>

                        <dt class="col-sm-5">Marque:</dt>
                        <dd class="col-sm-7">{{ $tire->brand }}</dd>

                        <dt class="col-sm-5">Modèle:</dt>
                        <dd class="col-sm-7">{{ $tire->model }}</dd>

                        <dt class="col-sm-5">Dimension:</dt>
                        <dd class="col-sm-7">{{ $tire->size }}</dd>

                        <dt class="col-sm-5">Véhicule:</dt>
                        <dd class="col-sm-7">
                            @if($tire->vehicle)
                                {{ $tire->vehicle->registration_number }}
                            @else
                                -
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Rotation Pattern Guide -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> Schéma de Rotation</h5>
                </div>
                <div class="card-body">
                    <p class="small mb-2"><strong>Rotation Standard:</strong></p>
                    <ul class="small mb-3">
                        <li>Avant Gauche → Arrière Droit</li>
                        <li>Avant Droit → Arrière Gauche</li>
                        <li>Arrière Gauche → Avant Gauche</li>
                        <li>Arrière Droit → Avant Droit</li>
                    </ul>

                    <p class="small mb-2"><strong>Rotation Directionnelle:</strong></p>
                    <ul class="small mb-0">
                        <li>Avant Gauche → Arrière Gauche</li>
                        <li>Avant Droit → Arrière Droit</li>
                        <li>Arrière Gauche → Avant Gauche</li>
                        <li>Arrière Droit → Avant Droit</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
