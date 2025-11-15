@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0"><i class="fas fa-tools"></i> Gestion des Interventions</h1>
            <p class="text-muted">Maintenance GMAO et suivi des réparations</p>
        </div>
        <div class="col-md-6 text-end">
            @can('create_interventions')
                <a href="{{ route('interventions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Intervention
                </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('interventions.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}"
                               placeholder="Titre, véhicule...">
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En Attente</option>
                            <option value="diagnostique" {{ request('status') == 'diagnostique' ? 'selected' : '' }}>Diagnostic</option>
                            <option value="en_reparation" {{ request('status') == 'en_reparation' ? 'selected' : '' }}>En Réparation</option>
                            <option value="cloture" {{ request('status') == 'cloture' ? 'selected' : '' }}>Clôturé</option>
                            <option value="annule" {{ request('status') == 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tous les types</option>
                            <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>Préventive</option>
                            <option value="curative" {{ request('type') == 'curative' ? 'selected' : '' }}>Curative</option>
                            <option value="predictive" {{ request('type') == 'predictive' ? 'selected' : '' }}>Prédictive</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="urgency" class="form-label">Urgence</label>
                        <select class="form-select" id="urgency" name="urgency">
                            <option value="">Toutes</option>
                            <option value="tres_urgent" {{ request('urgency') == 'tres_urgent' ? 'selected' : '' }}>Très Urgent</option>
                            <option value="urgent" {{ request('urgency') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="normal" {{ request('urgency') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="faible" {{ request('urgency') == 'faible' ? 'selected' : '' }}>Faible</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label for="urgent_only" class="form-label">Urgentes</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="urgent_only"
                                   name="urgent_only" value="1" {{ request('urgent_only') ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('interventions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Interventions Table -->
    <div class="card">
        <div class="card-header bg-warning">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Liste des Interventions
                <span class="badge bg-light text-dark">{{ $interventions->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($interventions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Véhicule</th>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Urgence</th>
                                <th>Statut</th>
                                <th>Demandeur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($interventions as $intervention)
                                <tr class="{{ $intervention->is_urgent ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>{{ $intervention->request_date->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $intervention->request_date->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $intervention->vehicle?->registration_number }}</strong>
                                        <br>
                                        <small class="text-muted">{{ number_format($intervention->mileage_at_request ?? 0, 0, ',', ' ') }} km</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ Str::limit($intervention->title, 40) }}</strong>
                                        </div>
                                        @if($intervention->category)
                                            <span class="badge bg-secondary">{{ $intervention->category->name }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $typeClass = match($intervention->type) {
                                                'preventive' => 'info',
                                                'curative' => 'warning',
                                                'predictive' => 'primary',
                                                default => 'secondary'
                                            };
                                            $typeLabel = match($intervention->type) {
                                                'preventive' => 'Préventive',
                                                'curative' => 'Curative',
                                                'predictive' => 'Prédictive',
                                                default => $intervention->type
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $typeClass }}">{{ $typeLabel }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $urgencyClass = match($intervention->urgency) {
                                                'tres_urgent' => 'danger',
                                                'urgent' => 'warning',
                                                'normal' => 'info',
                                                'faible' => 'secondary',
                                                default => 'secondary'
                                            };
                                            $urgencyLabel = match($intervention->urgency) {
                                                'tres_urgent' => 'Très Urgent',
                                                'urgent' => 'Urgent',
                                                'normal' => 'Normal',
                                                'faible' => 'Faible',
                                                default => $intervention->urgency
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $urgencyClass }}">
                                            @if($intervention->is_urgent)
                                                <i class="fas fa-exclamation-triangle"></i>
                                            @endif
                                            {{ $urgencyLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($intervention->status) {
                                                'en_attente' => 'warning',
                                                'diagnostique' => 'info',
                                                'en_reparation' => 'primary',
                                                'cloture' => 'success',
                                                'annule' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusLabel = match($intervention->status) {
                                                'en_attente' => 'En Attente',
                                                'diagnostique' => 'Diagnostic',
                                                'en_reparation' => 'En Réparation',
                                                'cloture' => 'Clôturé',
                                                'annule' => 'Annulé',
                                                default => $intervention->status
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td>{{ $intervention->requester?->full_name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('view_interventions')
                                                <a href="{{ route('interventions.show', $intervention) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @can('edit_interventions')
                                                <a href="{{ route('interventions.edit', $intervention) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete_interventions')
                                                <form action="{{ route('interventions.destroy', $intervention) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette intervention ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Affichage de {{ $interventions->firstItem() }} à {{ $interventions->lastItem() }}
                            sur {{ $interventions->total() }} interventions
                        </div>
                        <div>
                            {{ $interventions->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Aucune intervention trouvée</p>
                    @can('create_interventions')
                        <a href="{{ route('interventions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer une intervention
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
