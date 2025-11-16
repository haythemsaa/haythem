@extends('layouts.app')

@section('title', 'Journal d\'Audit')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-history"></i> Journal d'Audit</h1>
            <p class="text-muted">Historique complet des modifications du système</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('audit.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="event" class="form-label">Type d'Événement</label>
                    <select name="event" id="event" class="form-select">
                        <option value="">Tous</option>
                        <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Création</option>
                        <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Modification</option>
                        <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Suppression</option>
                        <option value="restored" {{ request('event') === 'restored' ? 'selected' : '' }}>Restauration</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="model_type" class="form-label">Type d'Entité</label>
                    <select name="model_type" id="model_type" class="form-select">
                        <option value="">Tous</option>
                        <option value="Vehicle" {{ request('model_type') === 'Vehicle' ? 'selected' : '' }}>Véhicules</option>
                        <option value="Intervention" {{ request('model_type') === 'Intervention' ? 'selected' : '' }}>Interventions</option>
                        <option value="Insurance" {{ request('model_type') === 'Insurance' ? 'selected' : '' }}>Assurances</option>
                        <option value="FuelConsumption" {{ request('model_type') === 'FuelConsumption' ? 'selected' : '' }}>Carburant</option>
                        <option value="Employee" {{ request('model_type') === 'Employee' ? 'selected' : '' }}>Employés</option>
                        <option value="User" {{ request('model_type') === 'User' ? 'selected' : '' }}>Utilisateurs</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="start_date" class="form-label">Date Début</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-md-2">
                    <label for="end_date" class="form-label">Date Fin</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Historique des Actions</h5>
            <span class="badge bg-primary">{{ $logs->total() }} entrées</span>
        </div>
        <div class="card-body p-0">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 180px;">Date/Heure</th>
                                <th style="width: 150px;">Utilisateur</th>
                                <th style="width: 120px;">Événement</th>
                                <th style="width: 150px;">Entité</th>
                                <th>Détails</th>
                                <th style="width: 120px;">IP</th>
                                <th style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <small>
                                            <i class="fas fa-calendar"></i> {{ $log->created_at->format('d/m/Y') }}<br>
                                            <i class="fas fa-clock"></i> {{ $log->created_at->format('H:i:s') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <i class="fas fa-user"></i> {{ $log->user->name ?? 'Système' }}
                                        @else
                                            <span class="text-muted">Système</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $eventClass = match($log->event) {
                                                'created' => 'success',
                                                'updated' => 'primary',
                                                'deleted' => 'danger',
                                                'restored' => 'info',
                                                default => 'secondary'
                                            };
                                            $eventIcon = match($log->event) {
                                                'created' => 'plus-circle',
                                                'updated' => 'edit',
                                                'deleted' => 'trash',
                                                'restored' => 'undo',
                                                default => 'circle'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $eventClass }}">
                                            <i class="fas fa-{{ $eventIcon }}"></i> {{ $log->event_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ class_basename($log->auditable_type) }}</strong><br>
                                        <small class="text-muted">ID: {{ $log->auditable_id }}</small>
                                    </td>
                                    <td>
                                        @if(count($log->changes) > 0)
                                            <small>
                                                @foreach(array_slice($log->changes, 0, 3, true) as $field => $change)
                                                    <strong>{{ $field }}:</strong>
                                                    @if($log->event === 'created')
                                                        <span class="text-success">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</span>
                                                    @elseif($log->event === 'updated')
                                                        <span class="text-danger">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</span>
                                                        →
                                                        <span class="text-success">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</span>
                                                    @elseif($log->event === 'deleted')
                                                        <span class="text-danger">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</span>
                                                    @endif
                                                    <br>
                                                @endforeach
                                                @if(count($log->changes) > 3)
                                                    <span class="text-muted">... et {{ count($log->changes) - 3 }} de plus</span>
                                                @endif
                                            </small>
                                        @else
                                            <small class="text-muted">Aucun changement détecté</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log->ip_address }}</small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $log->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Details Modal -->
                                <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-info-circle"></i> Détails de l'Audit #{{ $log->id }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Utilisateur:</strong> {{ $log->user->name ?? 'Système' }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Date:</strong> {{ $log->created_at->format('d/m/Y H:i:s') }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Événement:</strong> {{ $log->event_label }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Entité:</strong> {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>IP:</strong> {{ $log->ip_address }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>URL:</strong> <small>{{ $log->url }}</small>
                                                    </div>
                                                </div>

                                                <hr>

                                                <h6>Modifications:</h6>
                                                @if(count($log->changes) > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Champ</th>
                                                                    <th>Ancienne Valeur</th>
                                                                    <th>Nouvelle Valeur</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($log->changes as $field => $change)
                                                                    <tr>
                                                                        <td><strong>{{ $field }}</strong></td>
                                                                        <td>
                                                                            @if($change['old'] !== null)
                                                                                <code>{{ is_array($change['old']) ? json_encode($change['old'], JSON_PRETTY_PRINT) : $change['old'] }}</code>
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($change['new'] !== null)
                                                                                <code>{{ is_array($change['new']) ? json_encode($change['new'], JSON_PRETTY_PRINT) : $change['new'] }}</code>
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-muted">Aucune modification</p>
                                                @endif

                                                <hr>

                                                <details>
                                                    <summary><strong>User Agent</strong></summary>
                                                    <code>{{ $log->user_agent }}</code>
                                                </details>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune entrée d'audit pour ces critères</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
