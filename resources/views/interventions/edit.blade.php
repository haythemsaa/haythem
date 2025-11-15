@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-edit"></i> Modifier Intervention</h1>

    <form action="{{ route('interventions.update', $intervention) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Véhicule <span class="text-danger">*</span></label>
                        <select class="form-select" name="vehicle_id" required>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ $intervention->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Demandeur <span class="text-danger">*</span></label>
                        <select class="form-select" name="employee_id" required>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $intervention->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label>Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" value="{{ $intervention->title }}" required>
                    </div>
                    <div class="col-md-12">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ $intervention->description }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label>Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ $intervention->type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Urgence <span class="text-danger">*</span></label>
                        <select class="form-select" name="urgency" required>
                            @foreach($urgencies as $urgency)
                                <option value="{{ $urgency }}" {{ $intervention->urgency == $urgency ? 'selected' : '' }}>{{ ucfirst($urgency) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Sévérité</label>
                        <select class="form-select" name="severity">
                            <option value="">-</option>
                            @foreach($severities as $severity)
                                <option value="{{ $severity }}" {{ $intervention->severity == $severity ? 'selected' : '' }}>{{ ucfirst($severity) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Statut <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ $intervention->status == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Date Demande <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="request_date" value="{{ $intervention->request_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>Kilométrage</label>
                        <input type="number" class="form-control" name="mileage_at_request" value="{{ $intervention->mileage_at_request }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('interventions.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
