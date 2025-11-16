@extends('layouts.app')

@section('title', 'Calculateur TCO')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-calculator"></i> Calculateur TCO (Total Cost of Ownership)</h1>
            <p class="text-muted">Analysez le coût total de possession de vos véhicules sur une période donnée</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-sliders-h"></i> Paramètres de Calcul</h5>
                </div>
                <div class="card-body">
                    <form id="tcoForm" method="POST" action="{{ route('tco.calculate') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="vehicle_id" class="form-label">Véhicule <span class="text-danger">*</span></label>
                            <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                                <option value="">-- Sélectionner un véhicule --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->registration_number }} - {{ $vehicle->internal_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Date de début</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{ now()->subYear()->format('Y-m-d') }}">
                            <small class="text-muted">Laisser vide pour utiliser la date d'achat</small>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">Date de fin</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{ now()->format('Y-m-d') }}">
                            <small class="text-muted">Laisser vide pour aujourd'hui</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calculator"></i> Calculer TCO
                            </button>
                        </div>
                    </form>

                    <hr>

                    <h6 class="mb-3">Comparaison Multi-Véhicules</h6>
                    <form id="compareForm" method="POST" action="{{ route('tco.compare') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Sélectionner 2-5 véhicules</label>
                            <select name="vehicle_ids[]" class="form-select" multiple size="5">
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->registration_number }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Maintenez Ctrl/Cmd pour sélectionner plusieurs</small>
                        </div>

                        <div class="mb-3">
                            <input type="date" name="start_date" class="form-control mb-2"
                                   value="{{ now()->subYear()->format('Y-m-d') }}" placeholder="Date début">
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ now()->format('Y-m-d') }}" placeholder="Date fin">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-chart-bar"></i> Comparer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-info-circle text-info"></i> À propos du TCO</h6>
                    <p class="small mb-2">Le TCO inclut:</p>
                    <ul class="small mb-0">
                        <li>Acquisition & Dépréciation</li>
                        <li>Carburant</li>
                        <li>Maintenance & Réparations</li>
                        <li>Assurances</li>
                        <li>Taxes & Immatriculation</li>
                        <li>Accidents</li>
                        <li>Location/Leasing</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Résultats</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5" id="noResults">
                        <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Sélectionnez un véhicule et calculez le TCO</h5>
                        <p class="text-muted">Les résultats s'afficheront ici</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tcoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayTcoResults(data.data);
        }
    })
    .catch(error => console.error('Error:', error));
});

function displayTcoResults(tco) {
    document.getElementById('noResults').style.display = 'none';
    // Results will be displayed in the tco.result view
    window.location.href = '/tco?calculated=1';
}
</script>
@endpush
@endsection
