@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-certificate"></i> Détails de la Certification</h2>
        <div>
            @can('edit_employees')
            <a href="{{ route('certifications.edit', $certification) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('certifications.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations de la Certification</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Employé:</th>
                            <td>{{ $certification->employee->first_name }} {{ $certification->employee->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td><strong>{{ $certification->certification_type }}</strong></td>
                        </tr>
                        <tr>
                            <th>N° Certification:</th>
                            <td>{{ $certification->certification_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Organisme Émetteur:</th>
                            <td>{{ $certification->issuing_organization ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Date Émission:</th>
                            <td>{{ $certification->issue_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Date Expiration:</th>
                            <td>
                                @if($certification->expiry_date)
                                    {{ $certification->expiry_date->format('d/m/Y') }}
                                    @if($certification->days_until_expiry)
                                        <br><small class="text-muted">({{ $certification->days_until_expiry }} jours restants)</small>
                                    @elseif($certification->is_expired)
                                        <br><small class="text-danger">(Expiré)</small>
                                    @endif
                                @else
                                    <span class="text-muted">Permanent</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Statut:</th>
                            <td><span class="badge bg-{{ $certification->status_color }} fs-6">{{ $certification->status_label }}</span></td>
                        </tr>
                        @if($certification->attachment)
                        <tr>
                            <th>Document:</th>
                            <td>
                                <a href="{{ Storage::url($certification->attachment) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-pdf"></i> Voir le document
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if($certification->is_expired)
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-triangle"></i> Certification Expirée</h6>
                <p class="mb-0">Cette certification a expiré le {{ $certification->expiry_date->format('d/m/Y') }}. Veuillez la renouveler.</p>
            </div>
            @elseif($certification->is_expiring_soon)
            <div class="alert alert-warning">
                <h6><i class="fas fa-exclamation-triangle"></i> Expire Bientôt</h6>
                <p class="mb-0">Cette certification expire dans {{ $certification->days_until_expiry }} jours.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
