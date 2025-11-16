@extends('layouts.app')

@section('title', 'Suivi GPS')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-map-marked-alt"></i> Suivi GPS en Temps Réel</h1>
            <p class="text-muted">Localisez et suivez vos véhicules en temps réel</p>
        </div>
    </div>

    <!-- Map Container -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-map"></i> Carte de Localisation</h5>
        </div>
        <div class="card-body p-0">
            <div id="map" style="height: 600px;">
                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                    <div class="text-center">
                        <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                        <p class="text-muted">Intégration GPS - À venir</p>
                        <p class="small text-muted">Connexion aux dispositifs GPS requise</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle List -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-car"></i> Véhicules Actifs ({{ $vehicles->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                        @forelse($vehicles as $vehicle)
                            <a href="{{ route('gps.vehicle', $vehicle) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $vehicle->registration_number }}</h6>
                                        <small class="text-muted">
                                            {{ $vehicle->brand->name ?? '' }} {{ $vehicle->vehicleModel->name ?? '' }}
                                        </small>
                                    </div>
                                    <div>
                                        @if($vehicle->status === 'en_mission')
                                            <span class="badge bg-success">
                                                <i class="fas fa-circle"></i> En Mission
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-circle"></i> Disponible
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($vehicle->latestGpsLocation)
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-map-pin"></i>
                                        {{ $vehicle->latestGpsLocation->address ?? 'Position: ' . $vehicle->latestGpsLocation->coordinates }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        Mis à jour: {{ $vehicle->latestGpsLocation->recorded_at->diffForHumans() }}
                                    </small>
                                @else
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-map-pin"></i> Aucune position GPS
                                    </small>
                                @endif
                            </a>
                        @empty
                            <div class="list-group-item text-center py-5">
                                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun véhicule actif</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Statistiques GPS</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $vehicles->where('status', 'en_mission')->count() }}</h3>
                                <p class="text-muted mb-0">En Mission</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ $vehicles->where('status', 'disponible')->count() }}</h3>
                                <p class="text-muted mb-0">Disponibles</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-info">{{ $vehicles->count() }}</h3>
                                <p class="text-muted mb-0">Total Actifs</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">0</h3>
                                <p class="text-muted mb-0">Alertes</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-lightbulb"></i> Fonctionnalités GPS Disponibles</h6>
                        <ul class="mb-0">
                            <li>Suivi en temps réel de tous les véhicules</li>
                            <li>Historique des trajets</li>
                            <li>Alertes de géolocalisation</li>
                            <li>Rapport de kilométrage</li>
                            <li>Détection d'arrêts et de ralenti</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Configuration Requise:</strong>
                        Pour activer le suivi GPS, veuillez installer des dispositifs GPS compatibles sur vos véhicules et configurer l'API de suivi dans les paramètres système.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
