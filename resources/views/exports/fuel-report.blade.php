<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Carburant</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10px; }
        th { background-color: #28a745; color: white; padding: 6px; }
        td { border-bottom: 1px solid #ddd; padding: 6px; }
        .summary { background: #f8f9fa; padding: 15px; margin: 15px 0; }
    </style>
</head>
<body>
    <h1>⛽ Rapport Consommation Carburant</h1>
    
    <div class="summary">
        <strong>Total Coût:</strong> {{ number_format($total_cost, 2) }} DH<br>
        <strong>Total Quantité:</strong> {{ number_format($total_quantity, 2) }} L<br>
        <strong>Prix Moyen:</strong> {{ number_format($avg_price, 2) }} DH/L
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Véhicule</th>
                <th>Quantité (L)</th>
                <th>Prix Unit.</th>
                <th>Coût Total</th>
                <th>Station</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consumptions as $c)
            <tr>
                <td>{{ $c->refuel_date->format('d/m/Y') }}</td>
                <td>{{ $c->vehicle->registration_number ?? 'N/A' }}</td>
                <td style="text-align: right;">{{ number_format($c->quantity, 2) }}</td>
                <td style="text-align: right;">{{ number_format($c->unit_price, 2) }}</td>
                <td style="text-align: right;"><strong>{{ number_format($c->total_cost, 2) }} DH</strong></td>
                <td>{{ $c->station ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
