@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shield-alt"></i> Détails Assurance</h2>
        <a href="{{ route('insurances.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informations</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Véhicule:</dt>
                <dd class="col-sm-9">{{ $insurance->vehicle->registration_number ?? '-' }} {{ $insurance->vehicle->brand ?? '' }} {{ $insurance->vehicle->model ?? '' }}</dd>
                <dt class="col-sm-3">Compagnie:</dt>
                <dd class="col-sm-9">{{ $insurance->insurance_company }}</dd>
                <dt class="col-sm-3">N° Police:</dt>
                <dd class="col-sm-9">{{ $insurance->policy_number }}</dd>
                <dt class="col-sm-3">Type:</dt>
                <dd class="col-sm-9">{{ $insurance->insurance_type }}</dd>
                <dt class="col-sm-3">Couverture:</dt>
                <dd class="col-sm-9">{{ $insurance->coverage_type }}</dd>
                <dt class="col-sm-3">Date Début:</dt>
                <dd class="col-sm-9">{{ $insurance->start_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Date Fin:</dt>
                <dd class="col-sm-9">{{ $insurance->end_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Prime:</dt>
                <dd class="col-sm-9">{{ number_format($insurance->premium_amount, 2) }} DH</dd>
                <dt class="col-sm-3">Franchise:</dt>
                <dd class="col-sm-9">{{ $insurance->deductible ? number_format($insurance->deductible, 2) . ' DH' : '-' }}</dd>
                <dt class="col-sm-3">Statut:</dt>
                <dd class="col-sm-9"><span class="badge bg-{{ $insurance->status_color }}">{{ $insurance->status_label }}</span></dd>
            </dl>
        </div>
    </div>
</div>
@endsection
