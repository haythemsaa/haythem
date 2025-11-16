@extends('layouts.app')

@section('title', 'Paramètres Système')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-cog"></i> Paramètres Système</h1>
            <p class="text-muted">Configurez les paramètres de votre application</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Catégories</h6>
                </div>
                <div class="list-group list-group-flush" id="settingCategories">
                    <a href="#app" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="fas fa-desktop"></i> Application
                    </a>
                    <a href="#fleet" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-car"></i> Flotte
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                    <a href="#payments" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-credit-card"></i> Paiements
                    </a>
                    <a href="#fuel" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-gas-pump"></i> Carburant
                    </a>
                    <a href="#maintenance" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-tools"></i> Maintenance
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-shield-alt"></i> Sécurité
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')

                <div class="tab-content">
                    <!-- Application Settings -->
                    <div class="tab-pane fade show active" id="app">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-desktop"></i> Paramètres Application</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="app_name" class="form-label">Nom de l'Application</label>
                                    <input type="text" class="form-control" id="app_name" name="settings[app.name]"
                                           value="{{ setting('app.name', 'FleetManager Pro') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="app_locale" class="form-label">Langue par Défaut</label>
                                    <select class="form-select" id="app_locale" name="settings[app.locale]">
                                        <option value="fr" {{ setting('app.locale') === 'fr' ? 'selected' : '' }}>Français</option>
                                        <option value="ar" {{ setting('app.locale') === 'ar' ? 'selected' : '' }}>العربية</option>
                                        <option value="en" {{ setting('app.locale') === 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="app_currency" class="form-label">Devise</label>
                                    <input type="text" class="form-control" id="app_currency" name="settings[app.currency]"
                                           value="{{ setting('app.currency', 'DH') }}" placeholder="DH">
                                </div>

                                <div class="mb-3">
                                    <label for="app_timezone" class="form-label">Fuseau Horaire</label>
                                    <select class="form-select" id="app_timezone" name="settings[app.timezone]">
                                        <option value="Africa/Casablanca" {{ setting('app.timezone') === 'Africa/Casablanca' ? 'selected' : '' }}>Africa/Casablanca (GMT+1)</option>
                                        <option value="UTC" {{ setting('app.timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="Europe/Paris" {{ setting('app.timezone') === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="app_date_format" class="form-label">Format de Date</label>
                                    <select class="form-select" id="app_date_format" name="settings[app.date_format]">
                                        <option value="d/m/Y" {{ setting('app.date_format') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="Y-m-d" {{ setting('app.date_format') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                        <option value="m/d/Y" {{ setting('app.date_format') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fleet Settings -->
                    <div class="tab-pane fade" id="fleet">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-car"></i> Paramètres Flotte</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="fleet_maintenance_alert_days" class="form-label">Alerte Maintenance (jours avant échéance)</label>
                                    <input type="number" class="form-control" id="fleet_maintenance_alert_days"
                                           name="settings[fleet.maintenance_alert_days]"
                                           value="{{ setting('fleet.maintenance_alert_days', 30) }}">
                                    <small class="text-muted">Recevoir une alerte X jours avant une maintenance programmée</small>
                                </div>

                                <div class="mb-3">
                                    <label for="fleet_insurance_alert_days" class="form-label">Alerte Assurance (jours avant expiration)</label>
                                    <input type="number" class="form-control" id="fleet_insurance_alert_days"
                                           name="settings[fleet.insurance_alert_days]"
                                           value="{{ setting('fleet.insurance_alert_days', 30) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="fleet_document_alert_days" class="form-label">Alerte Documents (jours avant expiration)</label>
                                    <input type="number" class="form-control" id="fleet_document_alert_days"
                                           name="settings[fleet.document_alert_days]"
                                           value="{{ setting('fleet.document_alert_days', 15) }}">
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="fleet_auto_status_update"
                                           name="settings[fleet.auto_status_update]"
                                           {{ setting('fleet.auto_status_update', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fleet_auto_status_update">
                                        Mise à jour automatique du statut véhicule
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-bell"></i> Paramètres Notifications</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="notifications_email_enabled"
                                           name="settings[notifications.email_enabled]"
                                           {{ setting('notifications.email_enabled', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notifications_email_enabled">
                                        Activer les notifications par email
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="notifications_sms_enabled"
                                           name="settings[notifications.sms_enabled]"
                                           {{ setting('notifications.sms_enabled', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notifications_sms_enabled">
                                        Activer les notifications par SMS
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="notifications_from_email" class="form-label">Email Expéditeur</label>
                                    <input type="email" class="form-control" id="notifications_from_email"
                                           name="settings[notifications.from_email]"
                                           value="{{ setting('notifications.from_email', 'noreply@fleetmanager.com') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="notifications_from_name" class="form-label">Nom Expéditeur</label>
                                    <input type="text" class="form-control" id="notifications_from_name"
                                           name="settings[notifications.from_name]"
                                           value="{{ setting('notifications.from_name', 'FleetManager Pro') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Settings -->
                    <div class="tab-pane fade" id="payments">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-credit-card"></i> Paramètres Paiements</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="payments_stripe_enabled"
                                           name="settings[payments.stripe_enabled]"
                                           {{ setting('payments.stripe_enabled', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payments_stripe_enabled">
                                        Activer Stripe
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="payments_paypal_enabled"
                                           name="settings[payments.paypal_enabled]"
                                           {{ setting('payments.paypal_enabled', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payments_paypal_enabled">
                                        Activer PayPal
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="payments_cash_enabled"
                                           name="settings[payments.cash_enabled]"
                                           {{ setting('payments.cash_enabled', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payments_cash_enabled">
                                        Activer paiement en espèces
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="payments_tax_rate" class="form-label">Taux de TVA (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="payments_tax_rate"
                                           name="settings[payments.tax_rate]"
                                           value="{{ setting('payments.tax_rate', 20) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fuel Settings -->
                    <div class="tab-pane fade" id="fuel">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-gas-pump"></i> Paramètres Carburant</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="fuel_diesel_price" class="form-label">Prix Diesel (DH/L)</label>
                                    <input type="number" step="0.01" class="form-control" id="fuel_diesel_price"
                                           name="settings[fuel.diesel_price]"
                                           value="{{ setting('fuel.diesel_price', 13.50) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="fuel_gasoline_price" class="form-label">Prix Essence (DH/L)</label>
                                    <input type="number" step="0.01" class="form-control" id="fuel_gasoline_price"
                                           name="settings[fuel.gasoline_price]"
                                           value="{{ setting('fuel.gasoline_price', 15.20) }}">
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="fuel_anomaly_detection"
                                           name="settings[fuel.anomaly_detection_enabled]"
                                           {{ setting('fuel.anomaly_detection_enabled', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fuel_anomaly_detection">
                                        Activer la détection d'anomalies de consommation
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Settings -->
                    <div class="tab-pane fade" id="maintenance">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-tools"></i> Paramètres Maintenance</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="maintenance_oil_change_interval" class="form-label">Intervalle Vidange (KM)</label>
                                    <input type="number" class="form-control" id="maintenance_oil_change_interval"
                                           name="settings[maintenance.oil_change_interval]"
                                           value="{{ setting('maintenance.oil_change_interval', 10000) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="maintenance_tire_rotation_interval" class="form-label">Rotation Pneumatiques (KM)</label>
                                    <input type="number" class="form-control" id="maintenance_tire_rotation_interval"
                                           name="settings[maintenance.tire_rotation_interval]"
                                           value="{{ setting('maintenance.tire_rotation_interval', 15000) }}">
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenance_auto_schedule"
                                           name="settings[maintenance.auto_schedule_enabled]"
                                           {{ setting('maintenance.auto_schedule_enabled', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_auto_schedule">
                                        Planification automatique de la maintenance préventive
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Paramètres Sécurité</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="security_2fa_enabled"
                                           name="settings[security.2fa_enabled]"
                                           {{ setting('security.2fa_enabled', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="security_2fa_enabled">
                                        Activer l'authentification à deux facteurs (2FA)
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="security_audit_log"
                                           name="settings[security.audit_log_enabled]"
                                           {{ setting('security.audit_log_enabled', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="security_audit_log">
                                        Activer le journal d'audit
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="security_session_timeout" class="form-label">Délai d'expiration session (minutes)</label>
                                    <input type="number" class="form-control" id="security_session_timeout"
                                           name="settings[security.session_timeout]"
                                           value="{{ setting('security.session_timeout', 120) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Enregistrer les Paramètres
                        </button>
                        <button type="reset" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-undo"></i> Réinitialiser
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
