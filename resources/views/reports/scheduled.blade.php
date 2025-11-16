@extends('layouts.app')

@section('title', 'Rapports Planifiés')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-clock"></i> Rapports Planifiés</h1>
                <p class="text-muted">Automatisez la génération et l'envoi de vos rapports</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                <i class="fas fa-plus"></i> Nouveau Rapport Planifié
            </button>
        </div>
    </div>

    <!-- Scheduled Reports List -->
    <div class="card">
        <div class="card-body p-0">
            @if($scheduledReports->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Fréquence</th>
                                <th>Format</th>
                                <th>Destinataires</th>
                                <th>Prochaine Exécution</th>
                                <th>Dernière Exécution</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scheduledReports as $report)
                                <tr>
                                    <td><strong>{{ $report->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-{{ $report->report_type === 'fleet' ? 'car' : ($report->report_type === 'maintenance' ? 'tools' : ($report->report_type === 'fuel' ? 'gas-pump' : 'chart-line')) }}"></i>
                                            {{ $report->report_type_label }}
                                        </span>
                                    </td>
                                    <td>{{ $report->frequency_label }}</td>
                                    <td>
                                        <span class="badge bg-{{ $report->format === 'pdf' ? 'danger' : 'success' }}">
                                            {{ strtoupper($report->format) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ count($report->recipients) }} destinataire(s)</small>
                                    </td>
                                    <td>
                                        @if($report->next_run_at)
                                            {{ $report->next_run_at->format('d/m/Y H:i') }}
                                            <br><small class="text-muted">{{ $report->next_run_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->last_run_at)
                                            {{ $report->last_run_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Jamais</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $scheduledReports->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clock fa-4x text-muted mb-3"></i>
                    <h5>Aucun Rapport Planifié</h5>
                    <p class="text-muted">Créez votre premier rapport automatisé</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                        <i class="fas fa-plus"></i> Créer un Rapport Planifié
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('reports.schedule') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-clock"></i> Planifier un Rapport</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du Rapport</label>
                        <input type="text" class="form-control" id="name" name="name" required
                               placeholder="Ex: Rapport Mensuel Flotte">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="report_type" class="form-label">Type de Rapport</label>
                            <select class="form-select" id="report_type" name="report_type" required>
                                <option value="fleet">Flotte</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="fuel">Carburant</option>
                                <option value="financial">Financier</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="frequency" class="form-label">Fréquence</label>
                            <select class="form-select" id="frequency" name="frequency" required>
                                <option value="daily">Quotidien</option>
                                <option value="weekly">Hebdomadaire</option>
                                <option value="monthly" selected>Mensuel</option>
                                <option value="quarterly">Trimestriel</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="format" class="form-label">Format d'Export</label>
                        <select class="form-select" id="format" name="format" required>
                            <option value="pdf" selected>PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="recipients" class="form-label">Destinataires (Email)</label>
                        <textarea class="form-control" id="recipients" name="recipients[]" rows="3" required
                                  placeholder="Entrez un email par ligne&#10;exemple@domain.com&#10;autre@domain.com"></textarea>
                        <small class="text-muted">Un email par ligne</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Le rapport sera généré automatiquement et envoyé aux destinataires selon la fréquence choisie.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Convert textarea to array on form submit
    const form = document.querySelector('#scheduleModal form');
    form.addEventListener('submit', function(e) {
        const textarea = document.getElementById('recipients');
        const emails = textarea.value.split('\n').filter(email => email.trim() !== '');

        // Clear textarea and add hidden inputs for each email
        textarea.remove();
        emails.forEach(email => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'recipients[]';
            input.value = email.trim();
            form.appendChild(input);
        });
    });
});
</script>
@endpush
@endsection
