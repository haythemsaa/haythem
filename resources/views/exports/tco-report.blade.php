<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport TCO - {{ $tco['vehicle_registration'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; margin-bottom: 5px; }
        h2 { color: #666; font-size: 18px; margin-top: 25px; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #007bff; color: white; padding: 10px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 10px; }
        .amount { text-align: right; font-weight: bold; }
        .total-row { background-color: #f0f0f0; font-weight: bold; font-size: 14px; }
        .chart-bar { background-color: #007bff; height: 20px; border-radius: 3px; }
        .metric-box { display: inline-block; width: 30%; margin: 5px; padding: 15px; background: #f8f9fa; border-radius: 5px; text-align: center; }
        .metric-value { font-size: 24px; font-weight: bold; color: #007bff; }
        .metric-label { font-size: 11px; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>💰 Rapport TCO (Total Cost of Ownership)</h1>
    <div class="subtitle">
        <strong>Véhicule:</strong> {{ $tco['vehicle_registration'] }}<br>
        <strong>Période:</strong> {{ $tco['period']['start'] }} au {{ $tco['period']['end'] }}
        ({{ $tco['period']['days'] }} jours)
    </div>

    <h2>📊 Métriques Clés</h2>
    <div>
        <div class="metric-box">
            <div class="metric-value">{{ number_format($tco['total_cost'], 2) }} DH</div>
            <div class="metric-label">Coût Total</div>
        </div>
        <div class="metric-box">
            <div class="metric-value">{{ number_format($tco['metrics']['cost_per_day'], 2) }} DH</div>
            <div class="metric-label">Coût / Jour</div>
        </div>
        <div class="metric-box">
            <div class="metric-value">{{ number_format($tco['metrics']['cost_per_km'], 2) }} DH</div>
            <div class="metric-label">Coût / KM</div>
        </div>
    </div>

    <h2>💵 Répartition des Coûts</h2>
    <table>
        <thead>
            <tr>
                <th>Catégorie</th>
                <th class="amount">Montant (DH)</th>
                <th style="width: 30%;">Répartition</th>
                <th class="amount">%</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>🏷️ Acquisition</td>
                <td class="amount">{{ number_format($tco['costs']['acquisition'], 2) }}</td>
                <td>
                    <div class="chart-bar" style="width: {{ $tco['breakdown_percentage']['acquisition'] }}%;"></div>
                </td>
                <td class="amount">{{ $tco['breakdown_percentage']['acquisition'] }}%</td>
            </tr>
            <tr>
                <td>📉 Dépréciation</td>
                <td class="amount">{{ number_format($tco['costs']['depreciation'], 2) }}</td>
                <td>
                    <div class="chart-bar" style="width: {{ $tco['breakdown_percentage']['depreciation'] }}%;"></div>
                </td>
                <td class="amount">{{ $tco['breakdown_percentage']['depreciation'] }}%</td>
            </tr>
            <tr>
                <td>⛽ Carburant</td>
                <td class="amount">{{ number_format($tco['costs']['fuel'], 2) }}</td>
                <td>
                    <div class="chart-bar" style="width: {{ $tco['breakdown_percentage']['fuel'] }}%;"></div>
                </td>
                <td class="amount">{{ $tco['breakdown_percentage']['fuel'] }}%</td>
            </tr>
            <tr>
                <td>🔧 Maintenance</td>
                <td class="amount">{{ number_format($tco['costs']['maintenance'], 2) }}</td>
                <td>
                    <div class="chart-bar" style="width: {{ $tco['breakdown_percentage']['maintenance'] }}%;"></div>
                </td>
                <td class="amount">{{ $tco['breakdown_percentage']['maintenance'] }}%</td>
            </tr>
            <tr>
                <td>🛡️ Assurance</td>
                <td class="amount">{{ number_format($tco['costs']['insurance'], 2) }}</td>
                <td>
                    <div class="chart-bar" style="width: {{ $tco['breakdown_percentage']['insurance'] }}%;"></div>
                </td>
                <td class="amount">{{ $tco['breakdown_percentage']['insurance'] }}%</td>
            </tr>
            <tr>
                <td>📋 Taxes</td>
                <td class="amount">{{ number_format($tco['costs']['tax'], 2) }}</td>
                <td>
                    <div class="chart-bar" style="width: {{ $tco['breakdown_percentage']['tax'] }}%;"></div>
                </td>
                <td class="amount">{{ $tco['breakdown_percentage']['tax'] }}%</td>
            </tr>
            <tr>
                <td>🚨 Accidents</td>
                <td class="amount">{{ number_format($tco['costs']['accidents'], 2) }}</td>
                <td>
                    <div class="chart-bar" style="width: {{ $tco['breakdown_percentage']['accidents'] }}%;"></div>
                </td>
                <td class="amount">{{ $tco['breakdown_percentage']['accidents'] }}%</td>
            </tr>
            <tr>
                <td>🔄 Location/Leasing</td>
                <td class="amount">{{ number_format($tco['costs']['rental_leasing'], 2) }}</td>
                <td>
                    <div class="chart-bar" style="width: {{ $tco['breakdown_percentage']['rental_leasing'] }}%;"></div>
                </td>
                <td class="amount">{{ $tco['breakdown_percentage']['rental_leasing'] }}%</td>
            </tr>
            <tr class="total-row">
                <td colspan="2">COÛT TOTAL</td>
                <td colspan="2" class="amount">{{ number_format($tco['total_cost'], 2) }} DH</td>
            </tr>
        </tbody>
    </table>

    <h2>📈 Analyse</h2>
    <table>
        <tr>
            <td><strong>Kilométrage parcouru</strong></td>
            <td class="amount">{{ number_format($tco['metrics']['mileage_in_period']) }} km</td>
        </tr>
        <tr>
            <td><strong>Coût par mois</strong></td>
            <td class="amount">{{ number_format($tco['metrics']['cost_per_month'], 2) }} DH</td>
        </tr>
        <tr>
            <td><strong>Durée d'analyse</strong></td>
            <td class="amount">{{ $tco['period']['days'] }} jours ({{ $tco['period']['months'] }} mois)</td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #666; margin-top: 30px;">
        <p>FleetManager Pro - TCO Calculator | Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
