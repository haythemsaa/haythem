<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Assurances</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10px; }
        th { background-color: #6c757d; color: white; padding: 6px; }
        td { border-bottom: 1px solid #ddd; padding: 6px; }
        .expired { background-color: #f8d7da; }
        .expiring { background-color: #fff3cd; }
    </style>
</head>
<body>
    <h1>🛡️ Rapport Assurances</h1>
    
    <div style="background: #f8f9fa; padding: 15px; margin: 15px 0;">
        <strong>Total Prime Annuelle:</strong> {{ number_format($total_premium, 2) }} DH<br>
        <strong>Assurances Expirées:</strong> {{ $expired->count() }}<br>
        <strong>Expire Bientôt:</strong> {{ $expiring_soon->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Véhicule</th>
                <th>Police #</th>
                <th>Compagnie</th>
                <th>Type</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Prime</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($insurances as $ins)
            <tr class="{{ $ins->end_date->isPast() ? 'expired' : ($ins->end_date->diffInDays() < 30 ? 'expiring' : '') }}">
                <td>{{ $ins->vehicle->registration_number ?? 'N/A' }}</td>
                <td>{{ $ins->policy_number }}</td>
                <td>{{ $ins->supplier->name ?? 'N/A' }}</td>
                <td>{{ $ins->coverage_type ?? 'N/A' }}</td>
                <td>{{ $ins->start_date->format('d/m/Y') }}</td>
                <td>{{ $ins->end_date->format('d/m/Y') }}</td>
                <td style="text-align: right;">{{ number_format($ins->annual_premium, 2) }} DH</td>
                <td>
                    @if($ins->end_date->isPast())
                        ❌ Expiré
                    @elseif($ins->end_date->diffInDays() < 30)
                        ⚠️ Expire dans {{ $ins->end_date->diffInDays() }}j
                    @else
                        ✅ Actif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
