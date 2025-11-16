@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cogs"></i> Stock Pièces Détachées</h2>
        @can('create_vehicles')
        <div>
            <a href="{{ route('inventory-parts.alerts') }}" class="btn btn-warning me-2"><i class="fas fa-bell"></i> Alertes Stock</a>
            <a href="{{ route('inventory-parts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle Pièce</a>
        </div>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Total Pièces</h6><h3 class="mb-0">{{ $totalParts }}</h3></div>
                        <div class="text-primary"><i class="fas fa-cogs fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Stock Bas</h6><h3 class="mb-0">{{ $lowStockParts }}</h3></div>
                        <div class="text-warning"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Rupture Stock</h6><h3 class="mb-0">{{ $outOfStockParts }}</h3></div>
                        <div class="text-danger"><i class="fas fa-times-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Valeur Stock</h6><h3 class="mb-0">{{ number_format($totalStockValue, 0) }} DH</h3></div>
                        <div class="text-dark"><i class="fas fa-coins fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-list"></i> Liste des Pièces</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Réf. Pièce</th><th>Nom</th><th>Catégorie</th><th>Fournisseur</th><th>Stock</th><th>Min</th><th>Prix Unit.</th><th>Valeur</th><th>Statut</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($parts as $part)
                        <tr class="{{ $part->quantity_in_stock == 0 ? 'table-danger' : ($part->is_low_stock ? 'table-warning' : '') }}">
                            <td>{{ $part->part_number }}</td>
                            <td>{{ $part->part_name }}</td>
                            <td>{{ $part->category ?? '-' }}</td>
                            <td>{{ $part->supplier->name ?? '-' }}</td>
                            <td><strong>{{ $part->quantity_in_stock }}</strong></td>
                            <td>{{ $part->minimum_stock }}</td>
                            <td>{{ number_format($part->unit_price, 2) }} DH</td>
                            <td>{{ number_format($part->quantity_in_stock * $part->unit_price, 2) }} DH</td>
                            <td>
                                @if($part->quantity_in_stock == 0)
                                <span class="badge bg-danger">Rupture</span>
                                @elseif($part->is_low_stock)
                                <span class="badge bg-warning">Stock Bas</span>
                                @else
                                <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('inventory-parts.show', $part) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('inventory-parts.edit', $part) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center">Aucune pièce trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $parts->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
