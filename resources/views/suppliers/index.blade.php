@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-truck"></i> Fournisseurs</h2>
        @can('create_vehicles')
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau Fournisseur</a>
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
                        <div><h6 class="text-muted mb-1">Total</h6><h3 class="mb-0">{{ $totalSuppliers }}</h3></div>
                        <div class="text-primary"><i class="fas fa-truck fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Actifs</h6><h3 class="mb-0">{{ $activeSuppliers }}</h3></div>
                        <div class="text-success"><i class="fas fa-check-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-list"></i> Liste des Fournisseurs</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Code</th><th>Nom</th><th>Contact</th><th>Email</th><th>Téléphone</th><th>Ville</th><th>Statut</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->supplier_code }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                            <td>{{ $supplier->email ?? '-' }}</td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>{{ $supplier->city ?? '-' }}</td>
                            <td><span class="badge bg-{{ $supplier->is_active ? 'success' : 'secondary' }}">{{ $supplier->is_active ? 'Actif' : 'Inactif' }}</span></td>
                            <td>
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                @can('edit_vehicles')
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center">Aucun fournisseur trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $suppliers->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
