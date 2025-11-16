@extends('layouts.app')

@section('title', 'Centre de Notifications')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-bell"></i> Centre de Notifications</h1>
            <p class="text-muted">Gérez toutes vos alertes et notifications</p>
        </div>
    </div>

    <!-- Notification Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Critiques</h6>
                            <h2 class="mb-0 text-danger" id="criticalCount">0</h2>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-exclamation-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Avertissements</h6>
                            <h2 class="mb-0 text-warning" id="warningCount">0</h2>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Informations</h6>
                            <h2 class="mb-0 text-info" id="infoCount">0</h2>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-info-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total</h6>
                            <h2 class="mb-0 text-success" id="totalCount">0</h2>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-bell fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="filterLevel" id="filterAll" value="all" checked>
                        <label class="btn btn-outline-primary" for="filterAll">
                            <i class="fas fa-list"></i> Toutes
                        </label>

                        <input type="radio" class="btn-check" name="filterLevel" id="filterCritical" value="critical">
                        <label class="btn btn-outline-danger" for="filterCritical">
                            <i class="fas fa-exclamation-circle"></i> Critiques
                        </label>

                        <input type="radio" class="btn-check" name="filterLevel" id="filterWarning" value="warning">
                        <label class="btn btn-outline-warning" for="filterWarning">
                            <i class="fas fa-exclamation-triangle"></i> Avertissements
                        </label>

                        <input type="radio" class="btn-check" name="filterLevel" id="filterInfo" value="info">
                        <label class="btn btn-outline-info" for="filterInfo">
                            <i class="fas fa-info-circle"></i> Informations
                        </label>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-outline-secondary" id="markAllRead">
                        <i class="fas fa-check-double"></i> Tout marquer comme lu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list"></i> Notifications</h5>
        </div>
        <div class="card-body p-0">
            <div id="notificationsContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentFilter = 'all';
    let allAlerts = {
        critical: [],
        warnings: [],
        info: []
    };

    // Load alerts
    loadAlerts();

    // Filter change
    document.querySelectorAll('input[name="filterLevel"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentFilter = this.value;
            displayFilteredAlerts();
        });
    });

    // Mark all as read
    document.getElementById('markAllRead').addEventListener('click', function() {
        // In a real app, this would make an API call to mark notifications as read
        if (confirm('Marquer toutes les notifications comme lues ?')) {
            document.getElementById('notificationsContainer').innerHTML = `
                <div class="alert alert-success m-3" role="alert">
                    <i class="fas fa-check-circle"></i> Toutes les notifications ont été marquées comme lues
                </div>
            `;

            // Reset counts
            document.getElementById('criticalCount').textContent = '0';
            document.getElementById('warningCount').textContent = '0';
            document.getElementById('infoCount').textContent = '0';
            document.getElementById('totalCount').textContent = '0';
        }
    });

    function loadAlerts() {
        fetch('/api/dashboard/alerts', {
            headers: {
                'Authorization': 'Bearer {{ auth()->user()?->createToken("notifications")->plainTextToken ?? "" }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allAlerts.critical = data.data.critical || [];
                allAlerts.warnings = data.data.warnings || [];
                allAlerts.info = data.data.info || [];

                // Update counts
                document.getElementById('criticalCount').textContent = allAlerts.critical.length;
                document.getElementById('warningCount').textContent = allAlerts.warnings.length;
                document.getElementById('infoCount').textContent = allAlerts.info.length;
                document.getElementById('totalCount').textContent =
                    allAlerts.critical.length + allAlerts.warnings.length + allAlerts.info.length;

                displayFilteredAlerts();
            }
        })
        .catch(error => {
            console.error('Error loading alerts:', error);
            document.getElementById('notificationsContainer').innerHTML = `
                <div class="alert alert-danger m-3" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> Erreur lors du chargement des notifications
                </div>
            `;
        });
    }

    function displayFilteredAlerts() {
        const container = document.getElementById('notificationsContainer');
        let html = '';

        function renderAlerts(alerts, level, levelClass, icon) {
            if (currentFilter === 'all' || currentFilter === level) {
                alerts.forEach((alert, index) => {
                    html += `
                        <div class="list-group-item list-group-item-action alert-${levelClass} border-start border-${levelClass} border-5">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="fas fa-${icon}"></i>
                                        <strong>${alert.title || 'Notification'}</strong>
                                    </h6>
                                    <p class="mb-1">${alert.message}</p>
                                    ${alert.action_url ? `
                                        <a href="${alert.action_url}" class="btn btn-sm btn-outline-${levelClass} mt-2">
                                            <i class="fas fa-arrow-right"></i> Voir détails
                                        </a>
                                    ` : ''}
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-muted" onclick="this.closest('.list-group-item').remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> ${new Date().toLocaleDateString('fr-FR')}
                            </small>
                        </div>
                    `;
                });
            }
        }

        renderAlerts(allAlerts.critical, 'critical', 'danger', 'exclamation-circle');
        renderAlerts(allAlerts.warnings, 'warning', 'warning', 'exclamation-triangle');
        renderAlerts(allAlerts.info, 'info', 'info', 'info-circle');

        if (html === '') {
            html = `
                <div class="alert alert-success m-3" role="alert">
                    <i class="fas fa-check-circle"></i> Aucune notification ${currentFilter !== 'all' ? 'de ce type' : ''}
                </div>
            `;
        }

        container.innerHTML = `<div class="list-group list-group-flush">${html}</div>`;
    }
});
</script>
@endpush
@endsection
