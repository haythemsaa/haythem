@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-graduation-cap"></i> Détails de la Formation</h2>
        <div>
            @can('edit_trainings')
            <a href="{{ route('trainings.edit', $training) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('trainings.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations Générales</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Titre:</th>
                            <td><strong>{{ $training->title }}</strong></td>
                        </tr>
                        <tr>
                            <th>Employé:</th>
                            <td>{{ $training->employee->first_name }} {{ $training->employee->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>{{ $training->training_type }}</td>
                        </tr>
                        <tr>
                            <th>Organisme:</th>
                            <td>{{ $training->organization }}</td>
                        </tr>
                        @if($training->description)
                        <tr>
                            <th>Description:</th>
                            <td>{{ $training->description }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Date Début:</th>
                            <td>{{ $training->start_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Date Fin:</th>
                            <td>{{ $training->end_date ? $training->end_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @if($training->duration_days)
                        <tr>
                            <th>Durée:</th>
                            <td>{{ $training->duration_days }} jour(s)</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Durée (heures):</th>
                            <td>{{ $training->duration_hours ?? '-' }} heures</td>
                        </tr>
                        <tr>
                            <th>Coût:</th>
                            <td>{{ $training->cost ? number_format($training->cost, 2, ',', ' ') . ' DH' : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Statut:</th>
                            <td><span class="badge bg-{{ $training->status_color }} fs-6">{{ $training->status_label }}</span></td>
                        </tr>
                        @if($training->result)
                        <tr>
                            <th>Résultat:</th>
                            <td><span class="badge bg-{{ $training->result_color }} fs-6">{{ $training->result_label }}</span></td>
                        </tr>
                        @endif
                        @if($training->evaluation)
                        <tr>
                            <th>Évaluation:</th>
                            <td>{{ $training->evaluation }}</td>
                        </tr>
                        @endif
                        @if($training->certificate)
                        <tr>
                            <th>Certificat:</th>
                            <td>
                                <a href="{{ Storage::url($training->certificate) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-pdf"></i> Voir le certificat
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if($training->is_ongoing)
            <div class="alert alert-info">
                <h6><i class="fas fa-hourglass-half"></i> Formation En Cours</h6>
                <p class="mb-0">Cette formation est actuellement en cours.</p>
            </div>
            @elseif($training->is_upcoming)
            <div class="alert alert-warning">
                <h6><i class="fas fa-calendar"></i> Formation À Venir</h6>
                <p class="mb-0">Cette formation commencera le {{ $training->start_date->format('d/m/Y') }}.</p>
            </div>
            @elseif($training->is_completed)
            <div class="alert alert-success">
                <h6><i class="fas fa-check-circle"></i> Formation Terminée</h6>
                <p class="mb-0">Cette formation s'est terminée le {{ $training->end_date->format('d/m/Y') }}.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
