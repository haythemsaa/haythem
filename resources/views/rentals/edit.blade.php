@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-handshake"></i> Modifier Location</h2>
        <a href="{{ route('rentals.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card">
        <div class="card-header bg-warning text-white"><h5 class="mb-0">Informations Location</h5></div>
        <div class="card-body">
            <form action="{{ route('rentals.update', $rental) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Véhicule <span class="text-danger">*</span></label>
                        <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un véhicule</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $rental->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom Client <span class="text-danger">*</span></label>
                        <input type="text" name="client_name" class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name', $rental->client_name) }}" required>
                        @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone Client <span class="text-danger">*</span></label>
                        <input type="text" name="client_phone" class="form-control @error('client_phone') is-invalid @enderror" value="{{ old('client_phone', $rental->client_phone) }}" required>
                        @error('client_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Client</label>
                        <input type="email" name="client_email" class="form-control @error('client_email') is-invalid @enderror" value="{{ old('client_email', $rental->client_email) }}">
                        @error('client_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date Début <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $rental->start_date->format('Y-m-d')) }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date Fin <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $rental->end_date->format('Y-m-d')) }}" required>
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tarif Journalier (DH) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="daily_rate" class="form-control @error('daily_rate') is-invalid @enderror" value="{{ old('daily_rate', $rental->daily_rate) }}" required>
                        @error('daily_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Montant Caution (DH)</label>
                        <input type="number" step="0.01" name="deposit_amount" class="form-control @error('deposit_amount') is-invalid @enderror" value="{{ old('deposit_amount', $rental->deposit_amount) }}">
                        @error('deposit_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="reserved" {{ old('status', $rental->status) == 'reserved' ? 'selected' : '' }}>Réservé</option>
                            <option value="ongoing" {{ old('status', $rental->status) == 'ongoing' ? 'selected' : '' }}>En Cours</option>
                            <option value="completed" {{ old('status', $rental->status) == 'completed' ? 'selected' : '' }}>Terminé</option>
                            <option value="cancelled" {{ old('status', $rental->status) == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date Retour Réel</label>
                        <input type="date" name="actual_return_date" class="form-control @error('actual_return_date') is-invalid @enderror" value="{{ old('actual_return_date', $rental->actual_return_date ? $rental->actual_return_date->format('Y-m-d') : '') }}">
                        @error('actual_return_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $rental->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('rentals.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
