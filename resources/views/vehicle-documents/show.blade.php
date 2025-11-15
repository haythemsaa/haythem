@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8"><h1 class="h3"><i class="fas fa-file-alt"></i> {{ $vehicleDocument->document_type_name }}</h1></div>
        <div class="col-md-4 text-end">
            @can('edit_documents')
                <a href="{{ route('vehicle-documents.edit', $vehicleDocument) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Modifier</a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations Document</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Véhicule</th><td><strong>{{ $vehicleDocument->vehicle?->registration_number }}</strong></td></tr>
                        <tr><th>Type</th><td>{{ $vehicleDocument->document_type_name }}</td></tr>
                        <tr><th>N° Document</th><td>{{ $vehicleDocument->document_number ?? 'N/A' }}</td></tr>
                        <tr><th>Autorité</th><td>{{ $vehicleDocument->issuing_authority ?? 'N/A' }}</td></tr>
                        <tr><th>Date Emission</th><td>{{ $vehicleDocument->issue_date?->format('d/m/Y') ?? 'N/A' }}</td></tr>
                        <tr><th>Date Expiration</th><td>{{ $vehicleDocument->expiry_date?->format('d/m/Y') ?? 'N/A' }}</td></tr>
                        <tr><th>Statut</th><td><span class="badge bg-{{ $vehicleDocument->status_color }}">{{ $vehicleDocument->status_label }}</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-warning"><h5 class="mb-0">Fichier & Alertes</h5></div>
                <div class="card-body">
                    @if($vehicleDocument->file_path)
                        <div class="mb-3">
                            <strong>Fichier:</strong> {{ $vehicleDocument->file_name }}
                            <a href="{{ route('vehicle-documents.download', $vehicleDocument) }}" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                        </div>
                    @else
                        <p class="text-muted">Aucun fichier attaché</p>
                    @endif

                    @if($vehicleDocument->expiry_date)
                        <div class="alert alert-{{ $vehicleDocument->status_color }} mb-0">
                            @if($vehicleDocument->is_expired)
                                <i class="fas fa-exclamation-triangle"></i> <strong>Expiré depuis {{ abs($vehicleDocument->days_until_expiry) }} jours</strong>
                            @elseif($vehicleDocument->is_expiring_soon)
                                <i class="fas fa-clock"></i> <strong>Expire dans {{ $vehicleDocument->days_until_expiry }} jours</strong>
                            @else
                                <i class="fas fa-check-circle"></i> <strong>Valide - Expire dans {{ $vehicleDocument->days_until_expiry }} jours</strong>
                            @endif
                        </div>
                    @endif

                    @if($vehicleDocument->notes)
                        <div class="mt-3">
                            <strong>Notes:</strong>
                            <p>{{ $vehicleDocument->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
