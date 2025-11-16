@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cogs"></i> Détails Pièce</h2>
        <a href="{{ route('inventory-parts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations</h5></div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Référence Pièce:</dt><dd class="col-sm-9">{{ $inventoryPart->part_number }}</dd>
                <dt class="col-sm-3">Nom Pièce:</dt><dd class="col-sm-9">{{ $inventoryPart->part_name }}</dd>
                <dt class="col-sm-3">Catégorie:</dt><dd class="col-sm-9">{{ $inventoryPart->category ?? '-' }}</dd>
                <dt class="col-sm-3">Fournisseur:</dt><dd class="col-sm-9">{{ $inventoryPart->supplier->name ?? '-' }}</dd>
                <dt class="col-sm-3">Quantité en Stock:</dt><dd class="col-sm-9"><strong>{{ $inventoryPart->quantity_in_stock }}</strong> {{ $inventoryPart->unit ?? 'pièce(s)' }}</dd>
                <dt class="col-sm-3">Stock Minimum:</dt><dd class="col-sm-9">{{ $inventoryPart->minimum_stock }} {{ $inventoryPart->unit ?? 'pièce(s)' }}</dd>
                <dt class="col-sm-3">Prix Unitaire:</dt><dd class="col-sm-9">{{ number_format($inventoryPart->unit_price, 2) }} DH</dd>
                <dt class="col-sm-3">Valeur Stock:</dt><dd class="col-sm-9"><strong>{{ number_format($inventoryPart->quantity_in_stock * $inventoryPart->unit_price, 2) }} DH</strong></dd>
                <dt class="col-sm-3">Emplacement:</dt><dd class="col-sm-9">{{ $inventoryPart->location ?? '-' }}</dd>
                <dt class="col-sm-3">Description:</dt><dd class="col-sm-9">{{ $inventoryPart->description ?? '-' }}</dd>
                <dt class="col-sm-3">Statut:</dt>
                <dd class="col-sm-9">
                    @if($inventoryPart->quantity_in_stock == 0)
                    <span class="badge bg-danger">Rupture de Stock</span>
                    @elseif($inventoryPart->is_low_stock)
                    <span class="badge bg-warning">Stock Bas</span>
                    @else
                    <span class="badge bg-success">Stock OK</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>
@endsection
