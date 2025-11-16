@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-graduation-cap"></i> Nouvelle Formation</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Employé</label>
                        <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Titre</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Type</label>
                        <select name="training_type" class="form-select @error('training_type') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            @foreach($trainingTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('training_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('training_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Organisme</label>
                        <input type="text" name="organization" class="form-control @error('organization') is-invalid @enderror" value="{{ old('organization') }}" required>
                        @error('organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Date Début</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date Fin</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Durée (heures)</label>
                        <input type="number" name="duration_hours" class="form-control @error('duration_hours') is-invalid @enderror" value="{{ old('duration_hours') }}" min="0">
                        @error('duration_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Coût (DH)</label>
                        <input type="number" name="cost" step="0.01" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost') }}" min="0">
                        @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Résultat</label>
                        <select name="result" class="form-select @error('result') is-invalid @enderror">
                            <option value="">-</option>
                            @foreach($results as $key => $label)
                                <option value="{{ $key }}" {{ old('result') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('result')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Évaluation</label>
                        <textarea name="evaluation" rows="3" class="form-control @error('evaluation') is-invalid @enderror">{{ old('evaluation') }}</textarea>
                        @error('evaluation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Certificat</label>
                        <input type="file" name="certificate" class="form-control @error('certificate') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Formats: PDF, JPG, PNG (Max: 10 MB)</small>
                        @error('certificate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('trainings.index') }}" class="btn btn-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>.required::after { content: " *"; color: red; }</style>
@endsection
