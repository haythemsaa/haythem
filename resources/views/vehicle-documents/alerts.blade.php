@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">
        <i class="fas fa-bell text-warning"></i> Alertes Documents
        <span class="badge bg-danger">{{ $expiredDocuments->count() + $expiringSoonDocuments->count() }}</span>
    </h1>

    @if($expiredDocuments->count() > 0)
    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Documents Expirés ({{ $expiredDocuments->count() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Véhicule</th>
                            <th>Type Document</th>
                            <th>Date Expiration</th>
                            <th>Expiré Depuis</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiredDocuments as $doc)
                        <tr class="table-danger">
                            <td><strong>{{ $doc->vehicle?->registration_number }}</strong></td>
                            <td>{{ $doc->document_type_name }}</td>
                            <td>{{ $doc->expiry_date->format('d/m/Y') }}</td>
                            <td><strong>{{ abs($doc->days_until_expiry) }} jours</strong></td>
                            <td>
                                <a href="{{ route('vehicle-documents.edit', $doc) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-sync-alt"></i> Renouveler
                                </a>
                                <a href="{{ route('vehicle-documents.show', $doc) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($expiringSoonDocuments->count() > 0)
    <div class="card border-warning">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fas fa-clock"></i> Expire Bientôt - 30 Jours ({{ $expiringSoonDocuments->count() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Véhicule</th>
                            <th>Type Document</th>
                            <th>Date Expiration</th>
                            <th>Expire Dans</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringSoonDocuments as $doc)
                        <tr class="table-warning">
                            <td><strong>{{ $doc->vehicle?->registration_number }}</strong></td>
                            <td>{{ $doc->document_type_name }}</td>
                            <td>{{ $doc->expiry_date->format('d/m/Y') }}</td>
                            <td><strong>{{ $doc->days_until_expiry }} jours</strong></td>
                            <td>
                                <a href="{{ route('vehicle-documents.edit', $doc) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Mettre à jour
                                </a>
                                <a href="{{ route('vehicle-documents.show', $doc) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($expiredDocuments->count() === 0 && $expiringSoonDocuments->count() === 0)
    <div class="alert alert-success text-center">
        <i class="fas fa-check-circle fa-3x mb-3"></i>
        <h4>Aucune alerte</h4>
        <p>Tous les documents sont à jour !</p>
    </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('vehicle-documents.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>
@endsection
