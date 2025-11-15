@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3"><i class="fas fa-tools"></i> {{ $intervention->title }}</h1>
        </div>
        <div class="col-md-4 text-end">
            @can('edit_interventions')
                <a href="{{ route('interventions.edit', $intervention) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            @endcan
            @if($intervention->status != 'cloture')
                <form action="{{ route('interventions.close', $intervention) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Clôturer</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Véhicule</th><td><strong>{{ $intervention->vehicle?->registration_number }}</strong></td></tr>
                        <tr><th>Demandeur</th><td>{{ $intervention->requester?->full_name }}</td></tr>
                        <tr><th>Date Demande</th><td>{{ $intervention->request_date->format('d/m/Y') }}</td></tr>
                        <tr><th>Kilométrage</th><td>{{ number_format($intervention->mileage_at_request ?? 0, 0, ',', ' ') }} km</td></tr>
                        <tr><th>Catégorie</th><td>{{ $intervention->category?->name ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-warning"><h5 class="mb-0">Classification</h5></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Type</th>
                            <td><span class="badge bg-info">{{ ucfirst($intervention->type) }}</span></td>
                        </tr>
                        <tr>
                            <th>Urgence</th>
                            <td>
                                <span class="badge bg-{{ $intervention->is_urgent ? 'danger' : 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $intervention->urgency)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Sévérité</th>
                            <td>{{ $intervention->severity ? ucfirst($intervention->severity) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Statut</th>
                            <td>
                                <span class="badge bg-{{ $intervention->is_closed ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $intervention->status)) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-light"><h5 class="mb-0">Description</h5></div>
                <div class="card-body">
                    <p>{{ $intervention->description ?? 'Aucune description fournie.' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#diagnostic">Diagnostic</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#work-orders">Ordres de Travail</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#parts">Pièces Utilisées</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="diagnostic">
                            <p class="text-muted">Le diagnostic sera affiché ici une fois créé.</p>
                        </div>
                        <div class="tab-pane fade" id="work-orders">
                            <p class="text-muted">Les ordres de travail seront affichés ici.</p>
                        </div>
                        <div class="tab-pane fade" id="parts">
                            <p class="text-muted">Les pièces utilisées seront affichées ici.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
