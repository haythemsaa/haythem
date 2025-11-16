@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-handshake"></i> Détails Location</h2>
        <a href="{{ route('rentals.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Informations</h5></div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">N° Location:</dt><dd class="col-sm-9">{{ $rental->rental_number }}</dd>
                <dt class="col-sm-3">Client:</dt><dd class="col-sm-9">{{ $rental->client_name }}</dd>
                <dt class="col-sm-3">Téléphone:</dt><dd class="col-sm-9">{{ $rental->client_phone }}</dd>
                <dt class="col-sm-3">Véhicule:</dt><dd class="col-sm-9">{{ $rental->vehicle->registration_number ?? '-' }} {{ $rental->vehicle->brand ?? '' }} {{ $rental->vehicle->model ?? '' }}</dd>
                <dt class="col-sm-3">Date Début:</dt><dd class="col-sm-9">{{ $rental->start_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Date Fin:</dt><dd class="col-sm-9">{{ $rental->end_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-3">Retour Réel:</dt><dd class="col-sm-9">{{ $rental->actual_return_date ? $rental->actual_return_date->format('d/m/Y') : '-' }}</dd>
                <dt class="col-sm-3">Tarif Journalier:</dt><dd class="col-sm-9">{{ number_format($rental->daily_rate, 2) }} DH</dd>
                <dt class="col-sm-3">Nombre Jours:</dt><dd class="col-sm-9">{{ $rental->total_days }}</dd>
                <dt class="col-sm-3">Coût Total:</dt><dd class="col-sm-9">{{ number_format($rental->total_cost, 2) }} DH</dd>
                <dt class="col-sm-3">Caution:</dt><dd class="col-sm-9">{{ $rental->deposit_amount ? number_format($rental->deposit_amount, 2) . ' DH' : '-' }}</dd>
                <dt class="col-sm-3">Statut:</dt><dd class="col-sm-9"><span class="badge bg-{{ $rental->status_color }}">{{ $rental->status_label }}</span></dd>
            </dl>
        </div>
    </div>
</div>
@endsection
