<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiration Assurance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .alert-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .insurance-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .insurance-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .insurance-info td {
            padding: 8px 0;
        }
        .insurance-info td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .countdown {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🛡️</div>
            <h1>Alerte Expiration Assurance</h1>
            <p>Action Requise</p>
        </div>

        <div class="content">
            <p>Bonjour,</p>

            <div class="alert-box">
                <strong>⚠️ Assurance Expirant Bientôt</strong><br>
                L'assurance du véhicule suivant arrive à expiration. Veuillez renouveler dans les plus brefs délais.
            </div>

            <div class="countdown">
                ⏰ Expire dans {{ \Carbon\Carbon::parse($insurance->end_date)->diffInDays(now()) }} jours
            </div>

            <div class="insurance-info">
                <table>
                    <tr>
                        <td>Véhicule:</td>
                        <td><strong>{{ $insurance->vehicle->registration_number ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Marque/Modèle:</td>
                        <td>{{ $insurance->vehicle->brand->name ?? '' }} {{ $insurance->vehicle->vehicleModel->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Police N°:</td>
                        <td><strong>{{ $insurance->policy_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Assureur:</td>
                        <td>{{ $insurance->supplier->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Type Couverture:</td>
                        <td>{{ $insurance->coverage_type }}</td>
                    </tr>
                    <tr>
                        <td>Date Début:</td>
                        <td>{{ \Carbon\Carbon::parse($insurance->start_date)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Date Fin:</td>
                        <td><strong style="color: #dc3545;">{{ \Carbon\Carbon::parse($insurance->end_date)->format('d/m/Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Prime Annuelle:</td>
                        <td><strong>{{ number_format($insurance->annual_premium, 2) }} DH</strong></td>
                    </tr>
                </table>
            </div>

            <p><strong>Actions à prendre:</strong></p>
            <ul>
                <li>Contacter l'assureur pour renouvellement</li>
                <li>Comparer les offres concurrentes</li>
                <li>Vérifier les documents nécessaires</li>
                <li>Mettre à jour le système après renouvellement</li>
            </ul>

            <center>
                <a href="{{ config('app.url') }}/insurances/{{ $insurance->id }}/edit" class="btn">
                    Renouveler Maintenant
                </a>
            </center>

            <p style="margin-top: 30px; color: #dc3545;">
                <strong>⚠️ Important:</strong> Conduire sans assurance valide est illégal et expose votre entreprise à des risques financiers et juridiques importants.
            </p>

            <p>Cordialement,<br><strong>FleetManager Pro</strong></p>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} FleetManager Pro - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>
