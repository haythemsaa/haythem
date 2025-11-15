@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0"><i class="fas fa-user-plus"></i> Nouvel Employé</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employés</a></li>
                    <li class="breadcrumb-item active">Nouveau</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('employees.store') }}" method="POST">
        @csrf

        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Informations Personnelles</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="employee_code" class="form-label">
                            Code Employé <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('employee_code') is-invalid @enderror"
                               id="employee_code" name="employee_code"
                               value="{{ old('employee_code') }}" required>
                        @error('employee_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="first_name" class="form-label">
                            Prénom <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                               id="first_name" name="first_name"
                               value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="last_name" class="form-label">
                            Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                               id="last_name" name="last_name"
                               value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="birth_date" class="form-label">Date de Naissance</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                               id="birth_date" name="birth_date"
                               value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label">Adresse</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror"
                               id="address" name="address"
                               value="{{ old('address') }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="city" class="form-label">Ville</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                               id="city" name="city"
                               value="{{ old('city') }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="country" class="form-label">Pays</label>
                        <input type="text" class="form-control @error('country') is-invalid @enderror"
                               id="country" name="country"
                               value="{{ old('country', 'France') }}">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-address-book"></i> Contact</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone"
                               value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-briefcase"></i> Informations Professionnelles</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="position" class="form-label">Poste</label>
                        <select class="form-select @error('position') is-invalid @enderror"
                                id="position" name="position">
                            <option value="">Sélectionnez</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos }}" {{ old('position') == $pos ? 'selected' : '' }}>
                                    {{ ucfirst($pos) }}
                                </option>
                            @endforeach
                        </select>
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="department" class="form-label">Département</label>
                        <select class="form-select @error('department') is-invalid @enderror"
                                id="department" name="department">
                            <option value="">Sélectionnez</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ old('department') == $dept ? 'selected' : '' }}>
                                    {{ ucfirst($dept) }}
                                </option>
                            @endforeach
                        </select>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="site_id" class="form-label">Site</label>
                        <select class="form-select @error('site_id') is-invalid @enderror"
                                id="site_id" name="site_id">
                            <option value="">Sélectionnez</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('site_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="supervisor_id" class="form-label">Superviseur</label>
                        <select class="form-select @error('supervisor_id') is-invalid @enderror"
                                id="supervisor_id" name="supervisor_id">
                            <option value="">Aucun</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->first_name }} {{ $supervisor->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supervisor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="hire_date" class="form-label">Date d'Embauche</label>
                        <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                               id="hire_date" name="hire_date"
                               value="{{ old('hire_date', date('Y-m-d')) }}">
                        @error('hire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="contract_type" class="form-label">Type de Contrat</label>
                        <select class="form-select @error('contract_type') is-invalid @enderror"
                                id="contract_type" name="contract_type">
                            <option value="">Sélectionnez</option>
                            @foreach($contractTypes as $type)
                                <option value="{{ $type }}" {{ old('contract_type') == $type ? 'selected' : '' }}>
                                    {{ strtoupper($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('contract_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="base_salary" class="form-label">Salaire de Base (€)</label>
                        <input type="number" step="0.01" class="form-control @error('base_salary') is-invalid @enderror"
                               id="base_salary" name="base_salary"
                               value="{{ old('base_salary') }}">
                        @error('base_salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">
                            Statut <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                            <option value="actif" {{ old('status', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="suspendu" {{ old('status') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                            <option value="conge" {{ old('status') == 'conge' ? 'selected' : '' }}>Congé</option>
                            <option value="demission" {{ old('status') == 'demission' ? 'selected' : '' }}>Démission</option>
                            <option value="licencie" {{ old('status') == 'licencie' ? 'selected' : '' }}>Licencié</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- User Account Creation -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-user-lock"></i> Compte Utilisateur</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_user_account"
                                   name="create_user_account" value="1"
                                   {{ old('create_user_account') ? 'checked' : '' }}>
                            <label class="form-check-label" for="create_user_account">
                                Créer un compte utilisateur pour cet employé
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6" id="password_field" style="display: none;">
                        <label for="user_password" class="form-label">
                            Mot de passe <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control @error('user_password') is-invalid @enderror"
                               id="user_password" name="user_password" minlength="8">
                        <small class="text-muted">Minimum 8 caractères</small>
                        @error('user_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Show/hide password field based on checkbox
    document.getElementById('create_user_account').addEventListener('change', function() {
        const passwordField = document.getElementById('password_field');
        const passwordInput = document.getElementById('user_password');

        if (this.checked) {
            passwordField.style.display = 'block';
            passwordInput.required = true;
        } else {
            passwordField.style.display = 'none';
            passwordInput.required = false;
        }
    });

    // Trigger on page load
    if (document.getElementById('create_user_account').checked) {
        document.getElementById('password_field').style.display = 'block';
    }
</script>
@endpush
