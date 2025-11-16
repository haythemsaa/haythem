@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-heartbeat"></i> Modifier Visite Médicale</h2>
        <a href="{{ route('medical-checkups.show', $medicalCheckup) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('medical-checkups.update', $medicalCheckup) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-user"></i> Informations Employé</h5>

                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employé *</label>
                            <select class="form-select @error('employee_id') is-invalid @enderror"
                                    id="employee_id" name="employee_id" required>
                                <option value="">Sélectionner un employé</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ old('employee_id', $medicalCheckup->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="checkup_type" class="form-label">Type de Visite *</label>
                            <select class="form-select @error('checkup_type') is-invalid @enderror"
                                    id="checkup_type" name="checkup_type" required>
                                <option value="">Sélectionner un type</option>
                                @foreach($checkupTypes as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('checkup_type', $medicalCheckup->checkup_type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('checkup_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="checkup_date" class="form-label">Date de la Visite *</label>
                            <input type="date" class="form-control @error('checkup_date') is-invalid @enderror"
                                   id="checkup_date" name="checkup_date"
                                   value="{{ old('checkup_date', $medicalCheckup->checkup_date?->format('Y-m-d')) }}" required>
                            @error('checkup_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="next_checkup_date" class="form-label">Prochaine Visite</label>
                            <input type="date" class="form-control @error('next_checkup_date') is-invalid @enderror"
                                   id="next_checkup_date" name="next_checkup_date"
                                   value="{{ old('next_checkup_date', $medicalCheckup->next_checkup_date?->format('Y-m-d')) }}">
                            @error('next_checkup_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-stethoscope"></i> Résultat Médical</h5>

                        <div class="mb-3">
                            <label for="medical_center" class="form-label">Centre Médical *</label>
                            <input type="text" class="form-control @error('medical_center') is-invalid @enderror"
                                   id="medical_center" name="medical_center"
                                   value="{{ old('medical_center', $medicalCheckup->medical_center) }}" required>
                            @error('medical_center')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="doctor_name" class="form-label">Nom du Médecin</label>
                            <input type="text" class="form-control @error('doctor_name') is-invalid @enderror"
                                   id="doctor_name" name="doctor_name"
                                   value="{{ old('doctor_name', $medicalCheckup->doctor_name) }}">
                            @error('doctor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="result" class="form-label">Résultat *</label>
                            <select class="form-select @error('result') is-invalid @enderror"
                                    id="result" name="result" required>
                                <option value="">Sélectionner un résultat</option>
                                @foreach($results as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('result', $medicalCheckup->result) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('result')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="certificate_file" class="form-label">Certificat Médical</label>
                            @if($medicalCheckup->certificate_file)
                                <div class="mb-2">
                                    <a href="{{ route('medical-checkups.download', $medicalCheckup) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Télécharger le certificat actuel
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('certificate_file') is-invalid @enderror"
                                   id="certificate_file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                            @error('certificate_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">PDF, JPG, PNG (Max 10MB)</small>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-ban"></i> Restrictions</h5>

                        <div class="mb-3">
                            <label for="restrictions" class="form-label">Restrictions Médicales</label>
                            <textarea class="form-control @error('restrictions') is-invalid @enderror"
                                      id="restrictions" name="restrictions" rows="4">{{ old('restrictions', $medicalCheckup->restrictions) }}</textarea>
                            @error('restrictions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-comment"></i> Remarques</h5>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="4">{{ old('notes', $medicalCheckup->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('medical-checkups.show', $medicalCheckup) }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
