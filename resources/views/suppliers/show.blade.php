@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-truck"></i> Détails Fournisseur</h2>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations</h5></div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Code Fournisseur:</dt><dd class="col-sm-9">{{ $supplier->supplier_code }}</dd>
                <dt class="col-sm-3">Nom:</dt><dd class="col-sm-9">{{ $supplier->name }}</dd>
                <dt class="col-sm-3">Contact:</dt><dd class="col-sm-9">{{ $supplier->contact_person ?? '-' }}</dd>
                <dt class="col-sm-3">Email:</dt><dd class="col-sm-9">{{ $supplier->email ?? '-' }}</dd>
                <dt class="col-sm-3">Téléphone:</dt><dd class="col-sm-9">{{ $supplier->phone ?? '-' }}</dd>
                <dt class="col-sm-3">Adresse:</dt><dd class="col-sm-9">{{ $supplier->address ?? '-' }}</dd>
                <dt class="col-sm-3">Ville:</dt><dd class="col-sm-9">{{ $supplier->city ?? '-' }}</dd>
                <dt class="col-sm-3">Code Postal:</dt><dd class="col-sm-9">{{ $supplier->postal_code ?? '-' }}</dd>
                <dt class="col-sm-3">Pays:</dt><dd class="col-sm-9">{{ $supplier->country ?? '-' }}</dd>
                <dt class="col-sm-3">Statut:</dt><dd class="col-sm-9"><span class="badge bg-{{ $supplier->is_active ? 'success' : 'secondary' }}">{{ $supplier->is_active ? 'Actif' : 'Inactif' }}</span></dd>
                <dt class="col-sm-3">Notes:</dt><dd class="col-sm-9">{{ $supplier->notes ?? '-' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
