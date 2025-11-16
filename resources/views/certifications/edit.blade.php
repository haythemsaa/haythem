@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-edit"></i> Modifier la Certification</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('certifications.update', $certification) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Employé</label>
                        <select name="employee_id" class="form-select" required>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $certification->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Type de Certification</label>
                        <select name="name" class="form-select" required>
                            @foreach($certificationTypes as $key => $label)
                                <option value="{{ $key }}" {{ $certification->name == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">N° Certification</label>
                        <input type="text" name="certification_number" class="form-control" value="{{ $certification->certification_number }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Organisme Émetteur</label>
                        <input type="text" name="issuing_organization" class="form-control" value="{{ $certification->issuing_organization }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Date Émission</label>
                        <input type="date" name="issue_date" class="form-control" value="{{ $certification->issue_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date Expiration</label>
                        <input type="date" name="expiry_date" class="form-control" value="{{ $certification->expiry_date?->format('Y-m-d') }}">
                    </div>
                    @if($certification->attachment)
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Document Actuel</label>
                        <div>
                            <a href="{{ Storage::url($certification->attachment) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Voir le fichier
                            </a>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12 mb-3">
                        <label class="form-label">{{ $certification->attachment ? 'Remplacer le fichier' : 'Ajouter un fichier' }}</label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('certifications.show', $certification) }}" class="btn btn-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
