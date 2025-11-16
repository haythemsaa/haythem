@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell"></i> Alertes Stock</h2>
        <a href="{{ route('inventory-parts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white"><h5 class="mb-0"><i class="fas fa-times-circle"></i> Rupture de Stock ({{ $outOfStockParts->count() }})</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Réf. Pièce</th><th>Nom</th><th>Catégorie</th><th>Fournisseur</th><th>Stock</th><th>Min</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($outOfStockParts as $part)
                        <tr class="table-danger">
                            <td>{{ $part->part_number }}</td>
                            <td>{{ $part->part_name }}</td>
                            <td>{{ $part->category ?? '-' }}</td>
                            <td>{{ $part->supplier->name ?? '-' }}</td>
                            <td><strong class="text-danger">{{ $part->quantity_in_stock }}</strong></td>
                            <td>{{ $part->minimum_stock }}</td>
                            <td>
                                <a href="{{ route('inventory-parts.show', $part) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('inventory-parts.edit', $part) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-success">Aucune pièce en rupture de stock</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-warning">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Stock Bas ({{ $lowStockParts->count() }})</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Réf. Pièce</th><th>Nom</th><th>Catégorie</th><th>Fournisseur</th><th>Stock</th><th>Min</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($lowStockParts as $part)
                        <tr class="table-warning">
                            <td>{{ $part->part_number }}</td>
                            <td>{{ $part->part_name }}</td>
                            <td>{{ $part->category ?? '-' }}</td>
                            <td>{{ $part->supplier->name ?? '-' }}</td>
                            <td><strong class="text-warning">{{ $part->quantity_in_stock }}</strong></td>
                            <td>{{ $part->minimum_stock }}</td>
                            <td>
                                <a href="{{ route('inventory-parts.show', $part) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('inventory-parts.edit', $part) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-success">Aucune pièce avec stock bas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
