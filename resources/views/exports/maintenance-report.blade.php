<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Maintenance</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10px; }
        th { background-color: #007bff; color: white; padding: 6px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 6px; }
        .stats { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .stat-item { display: inline-block; width: 23%; text-align: center; }
        .stat-value { font-size: 20px; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <h1>🔧 Rapport de Maintenance</h1>
    <p style="text-align: center; color: #666;">Période d'analyse - Généré le {{ now()->format('d/m/Y') }}</p>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div>Total</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #ffc107;">{{ $stats['pending'] }}</div>
            <div>En Attente</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #17a2b8;">{{ $stats['in_progress'] }}</div>
            <div>En Cours</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #28a745;">{{ $stats['completed'] }}</div>
            <div>Complétées</div>
        </div>
    </div>

    <h3>💰 Coût Total: {{ number_format($total_cost, 2) }} DH</h3>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Véhicule</th>
                <th>Type</th>
                <th>Description</th>
                <th>Urgence</th>
                <th>Statut</th>
                <th>Coût</th>
            </tr>
        </thead>
        <tbody>
            @foreach($interventions as $intervention)
            <tr>
                <td>{{ $intervention->request_date->format('d/m/Y') }}</td>
                <td><strong>{{ $intervention->vehicle->registration_number ?? 'N/A' }}</strong></td>
                <td>{{ $intervention->type == 'preventive' ? '📅 Préventive' : '🚨 Curative' }}</td>
                <td>{{ Str::limit($intervention->title, 40) }}</td>
                <td>
                    @switch($intervention->urgency)
                        @case('tres_urgent') ⚡ Très Urgent @break
                        @case('urgent') ⚠️ Urgent @break
                        @case('normal') ℹ️ Normal @break
                        @default ⏱️ Peut Attendre
                    @endswitch
                </td>
                <td>
                    @switch($intervention->status)
                        @case('en_attente') ⏳ En Attente @break
                        @case('en_reparation') 🔧 En Cours @break
                        @case('cloture') ✅ Clôturée @break
                        @default {{ $intervention->status }}
                    @endswitch
                </td>
                <td style="text-align: right;">{{ number_format($intervention->workOrders->sum('total_cost'), 2) }} DH</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        FleetManager Pro - Rapport de Maintenance
    </div>
</body>
</html>
