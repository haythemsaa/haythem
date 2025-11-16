@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-chart-bar"></i> Centre de Rapports</h1>
            <p class="text-muted">Générez et planifiez vos rapports personnalisés</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('reports.custom') }}" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-plus-circle"></i> Créer un Rapport Personnalisé
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('reports.scheduled') }}" class="btn btn-success btn-lg w-100">
                <i class="fas fa-clock"></i> Rapports Planifiés
            </a>
        </div>
    </div>

    <!-- Predefined Reports -->
    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="fas fa-file-alt"></i> Rapports Prédéfinis</h4>
        </div>
    </div>

    <div class="row">
        @foreach($predefinedReports as $report)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded p-3">
                                    <i class="fas fa-{{ $report['icon'] }} fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">{{ $report['name'] }}</h5>
                                <p class="text-muted mb-0 small">{{ $report['description'] }}</p>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route($report['route']) }}" class="btn btn-outline-primary">
                                <i class="fas fa-file-pdf"></i> Générer PDF
                            </a>
                            <a href="{{ route($report['route'], ['format' => 'excel']) }}" class="btn btn-outline-success">
                                <i class="fas fa-file-excel"></i> Générer Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Scheduled Reports -->
    @if($scheduledReports->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h4><i class="fas fa-history"></i> Rapports Planifiés Récents</h4>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Fréquence</th>
                                <th>Prochaine Exécution</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scheduledReports->take(5) as $scheduled)
                                <tr>
                                    <td><strong>{{ $scheduled->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $scheduled->report_type_label }}</span>
                                    </td>
                                    <td>{{ $scheduled->frequency_label }}</td>
                                    <td>
                                        @if($scheduled->next_run_at)
                                            {{ $scheduled->next_run_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($scheduled->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('reports.scheduled') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($scheduledReports->count() > 5)
                    <div class="card-footer text-center">
                        <a href="{{ route('reports.scheduled') }}" class="btn btn-sm btn-outline-primary">
                            Voir tous les rapports planifiés
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
