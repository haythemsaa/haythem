@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-id-card"></i> Détails du Permis</h2>
        <div>
            @can('edit_employees')
            <a href="{{ route('driving-licenses.edit', $drivingLicense) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endcan
            <a href="{{ route('driving-licenses.index') }}" class="btn btn-secondary btn-sm">
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
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="30%">Employé:</th>
                            <td>{{ $drivingLicense->employee->first_name }} {{ $drivingLicense->employee->last_name }}</td>
                        </tr>
                        <tr>
                            <th>N° Permis:</th>
                            <td><strong>{{ $drivingLicense->license_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Catégories:</th>
                            <td>{{ $drivingLicense->categories_list }}</td>
                        </tr>
                        <tr>
                            <th>Date Émission:</th>
                            <td>{{ $drivingLicense->issue_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Date Expiration:</th>
                            <td>
                                @if($drivingLicense->expiry_date)
                                    {{ $drivingLicense->expiry_date->format('d/m/Y') }}
                                    @if($drivingLicense->days_until_expiry)
                                        <br><small>({{ $drivingLicense->days_until_expiry }} jours)</small>
                                    @endif
                                @else
                                    Permanent
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Points:</th>
                            <td><span class="badge bg-{{ $drivingLicense->points_color }} fs-5">{{ $drivingLicense->points }} / 12 points</span></td>
                        </tr>
                        <tr>
                            <th>Statut:</th>
                            <td><span class="badge bg-{{ $drivingLicense->status_color }}">{{ $drivingLicense->status_label }}</span></td>
                        </tr>
                        @if($drivingLicense->restrictions)
                        <tr>
                            <th>Restrictions:</th>
                            <td>{{ $drivingLicense->restrictions }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($violations->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Infractions Récentes ({{ $violations->count() }})</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Points Retirés</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($violations as $violation)
                            <tr>
                                <td>{{ $violation->violation_date->format('d/m/Y') }}</td>
                                <td>{{ $violation->violation_type_name }}</td>
                                <td><span class="badge bg-danger">-{{ $violation->points_deducted }} pts</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
