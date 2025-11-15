@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-edit"></i> Modifier le Permis</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('driving-licenses.update', $drivingLicense) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Employé</label>
                        <select name="employee_id" class="form-select" required>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $drivingLicense->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">N° Permis</label>
                        <input type="text" name="license_number" class="form-control" value="{{ $drivingLicense->license_number }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Date Émission</label>
                        <input type="date" name="issue_date" class="form-control" value="{{ $drivingLicense->issue_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date Expiration</label>
                        <input type="date" name="expiry_date" class="form-control" value="{{ $drivingLicense->expiry_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label required">Catégories</label><br>
                        @foreach($categories as $category)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category }}" 
                                    {{ in_array($category, $drivingLicense->categories ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $category }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Points</label>
                        <input type="number" name="points" class="form-control" value="{{ $drivingLicense->points }}" min="0" max="12">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('driving-licenses.show', $drivingLicense) }}" class="btn btn-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
