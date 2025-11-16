<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Programmée</title>
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
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .vehicle-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .vehicle-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .vehicle-info td {
            padding: 8px 0;
        }
        .vehicle-info td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔧</div>
            <h1>Maintenance Programmée</h1>
            <p>Rappel de Maintenance Véhicule</p>
        </div>

        <div class="content">
            <p>Bonjour,</p>

            <div class="alert-box">
                <strong>⚠️ Maintenance Requise</strong><br>
                Une intervention de maintenance est programmée pour le véhicule suivant.
            </div>

            <div class="vehicle-info">
                <table>
                    <tr>
                        <td>Véhicule:</td>
                        <td><strong>{{ $intervention->vehicle->registration_number ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Marque/Modèle:</td>
                        <td>{{ $intervention->vehicle->brand->name ?? '' }} {{ $intervention->vehicle->vehicleModel->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Type:</td>
                        <td>{{ $intervention->type === 'preventive' ? '📅 Préventive' : '🚨 Curative' }}</td>
                    </tr>
                    <tr>
                        <td>Titre:</td>
                        <td><strong>{{ $intervention->title }}</strong></td>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td>{{ $intervention->description ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Date Demande:</td>
                        <td>{{ $intervention->request_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Urgence:</td>
                        <td>
                            @switch($intervention->urgency)
                                @case('tres_urgent')
                                    <span style="color: #dc3545;">⚡ Très Urgent</span>
                                    @break
                                @case('urgent')
                                    <span style="color: #ffc107;">⚠️ Urgent</span>
                                    @break
                                @case('normal')
                                    <span style="color: #17a2b8;">ℹ️ Normal</span>
                                    @break
                                @default
                                    <span style="color: #6c757d;">⏱️ Peut Attendre</span>
                            @endswitch
                        </td>
                    </tr>
                </table>
            </div>

            <p>Veuillez planifier cette intervention dans les plus brefs délais pour assurer le bon fonctionnement du véhicule.</p>

            <center>
                <a href="{{ config('app.url') }}/interventions/{{ $intervention->id }}" class="btn">
                    Voir les détails
                </a>
            </center>

            <p style="margin-top: 30px;">Cordialement,<br><strong>FleetManager Pro</strong></p>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} FleetManager Pro - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>
