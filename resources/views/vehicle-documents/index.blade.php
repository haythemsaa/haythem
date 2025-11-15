@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0"><i class="fas fa-file-alt"></i> Documents Véhicules</h1>
            <p class="text-muted">Gestion des documents administratifs</p>
        </div>
        <div class="col-md-6 text-end">
            @can('create_documents')
                <a href="{{ route('vehicle-documents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Document
                </a>
                <a href="{{ route('vehicle-documents.alerts') }}" class="btn btn-warning">
                    <i class="fas fa-bell"></i> Alertes 
                    @if($expiringSoonDocuments + $expiredDocuments > 0)
                        <span class="badge bg-danger">{{ $expiringSoonDocuments + $expiredDocuments }}</span>
                    @endif
                </a>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Total Documents</h6>
                    <h3 class="text-primary">{{ $totalDocuments ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Valides</h6>
                    <h3 class="text-success">{{ $validDocuments ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Expire Bientôt</h6>
                    <h3 class="text-warning">{{ $expiringSoonDocuments ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="text-muted">Expirés</h6>
                    <h3 class="text-danger">{{ $expiredDocuments ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light"><h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5></div>
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>Recherche</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="N° document, véhicule...">
                    </div>
                    <div class="col-md-2">
                        <label>Véhicule</label>
                        <select class="form-select" name="vehicle_id">
                            <option value="">Tous</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->registration_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Type Document</label>
                        <select class="form-select" name="document_type">
                            <option value="">Tous</option>
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('document_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Statut</label>
                        <select class="form-select" name="status">
                            <option value="">Tous</option>
                            <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Valide</option>
                            <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>Expire Bientôt</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expiré</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i> Filtrer</button>
                        <a href="{{ route('vehicle-documents.index') }}" class="btn btn-outline-secondary"><i class="fas fa-redo"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Documents <span class="badge bg-light text-dark">{{ $documents->total() }}</span></h5>
        </div>
        <div class="card-body p-0">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Véhicule</th>
                                <th>Type Document</th>
                                <th>N° Document</th>
                                <th>Date Emission</th>
                                <th>Date Expiration</th>
                                <th>Statut</th>
                                <th>Fichier</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                                <tr class="{{ $doc->is_expired ? 'table-danger' : ($doc->is_expiring_soon ? 'table-warning' : '') }}">
                                    <td><strong>{{ $doc->vehicle?->registration_number }}</strong></td>
                                    <td>{{ $doc->document_type_name }}</td>
                                    <td>{{ $doc->document_number ?? 'N/A' }}</td>
                                    <td>{{ $doc->issue_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td>
                                        <div>{{ $doc->expiry_date?->format('d/m/Y') ?? 'N/A' }}</div>
                                        @if($doc->days_until_expiry !== null)
                                            @if($doc->is_expired)
                                                <small class="text-danger">Expiré depuis {{ abs($doc->days_until_expiry) }} jours</small>
                                            @elseif($doc->is_expiring_soon)
                                                <small class="text-warning">Expire dans {{ $doc->days_until_expiry }} jours</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td><span class="badge bg-{{ $doc->status_color }}">{{ $doc->status_label }}</span></td>
                                    <td>
                                        @if($doc->file_path)
                                            <a href="{{ route('vehicle-documents.download', $doc) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @can('view_documents')
                                                <a href="{{ route('vehicle-documents.show', $doc) }}" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                                            @endcan
                                            @can('edit_documents')
                                                <a href="{{ route('vehicle-documents.edit', $doc) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                            @endcan
                                            @can('delete_documents')
                                                <form action="{{ route('vehicle-documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $documents->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Aucun document</p>
                    @can('create_documents')
                        <a href="{{ route('vehicle-documents.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
