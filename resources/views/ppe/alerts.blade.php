@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell"></i> Alertes EPI</h2>
        <a href="{{ route('ppe.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card border-danger mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-times-circle"></i> EPI Expirés ({{ $expiredEquipment->count() }})</h5>
        </div>
        <div class="card-body">
            @if($expiredEquipment->count() > 0)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <strong>ATTENTION:</strong> Ces équipements sont expirés et doivent être remplacés immédiatement.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Date Expiration</th>
                            <th>Jours Dépassés</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiredEquipment as $item)
                        <tr>
                            <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                            <td>{{ $item->equipment_type }}</td>
                            <td>{{ $item->equipment_name }}</td>
                            <td>{{ $item->expiry_date->format('d/m/Y') }}</td>
                            <td class="text-danger"><strong>{{ $item->expiry_date->diffInDays(now()) }} jours</strong></td>
                            <td><span class="badge bg-{{ $item->condition_color }}">{{ $item->condition_label }}</span></td>
                            <td><a href="{{ route('ppe.show', $item) }}" class="btn btn-sm btn-info">Voir</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucun EPI expiré</p>
            @endif
        </div>
    </div>

    <div class="card border-warning mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> EPI Expirant Bientôt ({{ $expiringSoonEquipment->count() }})</h5>
        </div>
        <div class="card-body">
            @if($expiringSoonEquipment->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Date Expiration</th>
                            <th>Jours Restants</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringSoonEquipment as $item)
                        <tr>
                            <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                            <td>{{ $item->equipment_type }}</td>
                            <td>{{ $item->equipment_name }}</td>
                            <td>{{ $item->expiry_date->format('d/m/Y') }}</td>
                            <td class="text-warning"><strong>{{ $item->days_until_expiry }} jours</strong></td>
                            <td><span class="badge bg-{{ $item->condition_color }}">{{ $item->condition_label }}</span></td>
                            <td><a href="{{ route('ppe.show', $item) }}" class="btn btn-sm btn-info">Voir</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucun EPI expirant bientôt</p>
            @endif
        </div>
    </div>

    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-tools"></i> EPI Endommagés ({{ $damagedEquipment->count() }})</h5>
        </div>
        <div class="card-body">
            @if($damagedEquipment->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Date Attribution</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($damagedEquipment as $item)
                        <tr>
                            <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}</td>
                            <td>{{ $item->equipment_type }}</td>
                            <td>{{ $item->equipment_name }}</td>
                            <td>{{ $item->issue_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-{{ $item->status_color }}">{{ $item->status_label }}</span></td>
                            <td><a href="{{ route('ppe.show', $item) }}" class="btn btn-sm btn-info">Voir</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-success mb-0"><i class="fas fa-check-circle"></i> Aucun EPI endommagé</p>
            @endif
        </div>
    </div>
</div>
@endsection
