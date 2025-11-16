@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-edit"></i> Modifier la Formation</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('trainings.update', $training) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Employé</label>
                        <select name="employee_id" class="form-select" required>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $training->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Titre</label>
                        <input type="text" name="title" class="form-control" value="{{ $training->title }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Type</label>
                        <select name="training_type" class="form-select" required>
                            @foreach($trainingTypes as $key => $label)
                                <option value="{{ $key }}" {{ $training->training_type == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Organisme</label>
                        <input type="text" name="organization" class="form-control" value="{{ $training->organization }}" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control">{{ $training->description }}</textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Date Début</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $training->start_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date Fin</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $training->end_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Durée (heures)</label>
                        <input type="number" name="duration_hours" class="form-control" value="{{ $training->duration_hours }}" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Coût (DH)</label>
                        <input type="number" name="cost" step="0.01" class="form-control" value="{{ $training->cost }}" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Résultat</label>
                        <select name="result" class="form-select">
                            <option value="">-</option>
                            @foreach($results as $key => $label)
                                <option value="{{ $key }}" {{ $training->result == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Évaluation</label>
                        <textarea name="evaluation" rows="3" class="form-control">{{ $training->evaluation }}</textarea>
                    </div>
                    @if($training->certificate)
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Certificat Actuel</label>
                        <div>
                            <a href="{{ Storage::url($training->certificate) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Voir le certificat
                            </a>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12 mb-3">
                        <label class="form-label">{{ $training->certificate ? 'Remplacer le certificat' : 'Ajouter un certificat' }}</label>
                        <input type="file" name="certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('trainings.show', $training) }}" class="btn btn-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
