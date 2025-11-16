<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Résumé Flotte - {{ now()->format('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; font-size: 24px; margin-bottom: 10px; }
        h2 { color: #666; font-size: 18px; margin-top: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #007bff; color: white; padding: 8px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 8px; }
        tr:hover { background-color: #f5f5f5; }
        .stats-box { display: inline-block; width: 23%; margin: 5px; padding: 15px; background: #f8f9fa; border-radius: 5px; text-align: center; }
        .stat-value { font-size: 28px; font-weight: bold; color: #007bff; }
        .stat-label { font-size: 12px; color: #666; margin-top: 5px; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Résumé de la Flotte</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <h2>Statistiques Globales</h2>
    <div style="margin-bottom: 20px;">
        <div class="stats-box">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Véhicules</div>
        </div>
        <div class="stats-box">
            <div class="stat-value" style="color: #28a745;">{{ $stats['available'] }}</div>
            <div class="stat-label">Disponibles</div>
        </div>
        <div class="stats-box">
            <div class="stat-value" style="color: #ffc107;">{{ $stats['in_service'] }}</div>
            <div class="stat-label">En Service</div>
        </div>
        <div class="stats-box">
            <div class="stat-value" style="color: #dc3545;">{{ $stats['maintenance'] }}</div>
            <div class="stat-label">En Maintenance</div>
        </div>
    </div>

    <h2>Liste des Véhicules</h2>
    <table>
        <thead>
            <tr>
                <th>Immatriculation</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Année</th>
                <th>Kilométrage</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $vehicle)
            <tr>
                <td><strong>{{ $vehicle->registration_number }}</strong></td>
                <td>{{ $vehicle->brand->name ?? 'N/A' }}</td>
                <td>{{ $vehicle->vehicleModel->name ?? 'N/A' }}</td>
                <td>{{ $vehicle->year }}</td>
                <td>{{ number_format($vehicle->current_mileage) }} km</td>
                <td>
                    @switch($vehicle->status)
                        @case('disponible') ✅ Disponible @break
                        @case('en_mission') 🚗 En Mission @break
                        @case('en_maintenance') 🔧 Maintenance @break
                        @case('en_panne') ⛔ En Panne @break
                        @default {{ $vehicle->status }}
                    @endswitch
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>FleetManager Pro - Gestion Intelligente de Flotte | Page {PAGE_NUM} / {PAGE_COUNT}</p>
    </div>
</body>
</html>
