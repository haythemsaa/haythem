@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-hard-hat"></i> Modifier EPI</h2>
        <a href="{{ route('ppe.show', $ppe) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('ppe.update', $ppe) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-user"></i> Attribution</h5>
                        
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employé *</label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                <option value="">Sélectionner un employé</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $ppe->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="equipment_type" class="form-label">Type EPI *</label>
                            <select class="form-select @error('equipment_type') is-invalid @enderror" id="equipment_type" name="equipment_type" required>
                                <option value="">Sélectionner un type</option>
                                @foreach($equipmentTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('equipment_type', $ppe->equipment_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('equipment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="equipment_name" class="form-label">Nom Équipement *</label>
                            <input type="text" class="form-control @error('equipment_name') is-invalid @enderror" id="equipment_name" name="equipment_name" value="{{ old('equipment_name', $ppe->equipment_name) }}" required>
                            @error('equipment_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="brand" class="form-label">Marque</label>
                            <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand', $ppe->brand) }}">
                            @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="size" class="form-label">Taille</label>
                            <input type="text" class="form-control @error('size') is-invalid @enderror" id="size" name="size" value="{{ old('size', $ppe->size) }}">
                            @error('size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-calendar"></i> Dates & Statut</h5>

                        <div class="mb-3">
                            <label for="issue_date" class="form-label">Date Attribution *</label>
                            <input type="date" class="form-control @error('issue_date') is-invalid @enderror" id="issue_date" name="issue_date" value="{{ old('issue_date', $ppe->issue_date?->format('Y-m-d')) }}" required>
                            @error('issue_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="replacement_frequency_months" class="form-label">Fréquence Remplacement (mois)</label>
                            <input type="number" class="form-control @error('replacement_frequency_months') is-invalid @enderror" id="replacement_frequency_months" name="replacement_frequency_months" value="{{ old('replacement_frequency_months', $ppe->replacement_frequency_months) }}" min="1" max="120">
                            @error('replacement_frequency_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Date Expiration</label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $ppe->expiry_date?->format('Y-m-d')) }}">
                            @error('expiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="condition" class="form-label">État *</label>
                            <select class="form-select @error('condition') is-invalid @enderror" id="condition" name="condition" required>
                                @foreach($conditions as $key => $label)
                                    <option value="{{ $key }}" {{ old('condition', $ppe->condition) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $ppe->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="serial_number" class="form-label">Numéro de Série</label>
                            <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" value="{{ old('serial_number', $ppe->serial_number) }}">
                            @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="cost" class="form-label">Coût (DH)</label>
                            <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" id="cost" name="cost" value="{{ old('cost', $ppe->cost) }}">
                            @error('cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes', $ppe->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('ppe.show', $ppe) }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
