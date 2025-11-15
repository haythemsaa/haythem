@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-id-card"></i> Nouveau Permis de Conduire</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('driving-licenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Employé</label>
                        <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">N° Permis</label>
                        <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror" required>
                        @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Date Émission</label>
                        <input type="date" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror" required>
                        @error('issue_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date Expiration</label>
                        <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror">
                        @error('expiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label required">Catégories</label><br>
                        @foreach($categories as $category)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category }}" id="cat{{ $category }}">
                                <label class="form-check-label" for="cat{{ $category }}">{{ $category }}</label>
                            </div>
                        @endforeach
                        @error('categories')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Points</label>
                        <input type="number" name="points" class="form-control" value="12" min="0" max="12">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Pièce Jointe</label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('driving-licenses.index') }}" class="btn btn-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>.required::after { content: " *"; color: red; }</style>
@endsection
