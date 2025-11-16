@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-contract"></i> Détails Contrat</h2>
        <a href="{{ route('contracts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations</h5></div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">N° Contrat:</dt><dd class="col-sm-9">{{ $contract->contract_number }}</dd>
                <dt class="col-sm-3">Type:</dt><dd class="col-sm-9">{{ $contract->contract_type }}</dd>
                <dt class="col-sm-3">Fournisseur:</dt><dd class="col-sm-9">{{ $contract->supplier_name }}</dd>
                <dt class="col-sm-3">Véhicule:</dt><dd class="col-sm-9">{{ $contract->vehicle->registration_number ?? '-' }}</dd>
                <dt class="col-sm-3">Date Début:</dt><dd class="col-sm-9">{{ $contract->start_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Date Fin:</dt><dd class="col-sm-9">{{ $contract->end_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Coût Mensuel:</dt><dd class="col-sm-9">{{ number_format($contract->monthly_cost, 2) }} DH</dd>
                <dt class="col-sm-3">Renouvellement Auto:</dt><dd class="col-sm-9">{{ $contract->auto_renewal ? 'Oui' : 'Non' }}</dd>
                <dt class="col-sm-3">Statut:</dt><dd class="col-sm-9"><span class="badge bg-{{ $contract->status_color }}">{{ $contract->status_label }}</span></dd>
            </dl>
        </div>
    </div>
</div>
@endsection
