<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déclaration d'Accident</title>
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
            background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
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
            background: #ffe3e3;
            border-left: 4px solid #ff6b6b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .accident-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .accident-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .accident-info td {
            padding: 8px 0;
        }
        .accident-info td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .severity-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .severity-critical {
            background: #ff6b6b;
            color: white;
        }
        .severity-major {
            background: #ffa94d;
            color: white;
        }
        .severity-minor {
            background: #ffe066;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #ff6b6b;
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
        .checklist {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🚨</div>
            <h1>Déclaration d'Accident</h1>
            <p>Notification Urgente</p>
        </div>

        <div class="content">
            <p>Bonjour,</p>

            <div class="alert-box">
                <strong>🚨 Nouvel Accident Déclaré</strong><br>
                Un accident impliquant un véhicule de la flotte a été enregistré dans le système.
            </div>

            <div class="accident-info">
                <table>
                    <tr>
                        <td>Date Accident:</td>
                        <td><strong>{{ \Carbon\Carbon::parse($accident->accident_date)->format('d/m/Y H:i') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Véhicule:</td>
                        <td><strong>{{ $accident->vehicle->registration_number ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Conducteur:</td>
                        <td>{{ $accident->driver->full_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Lieu:</td>
                        <td>{{ $accident->location ?? 'Non spécifié' }}</td>
                    </tr>
                    <tr>
                        <td>Gravité:</td>
                        <td>
                            @php
                                $severityClass = match($accident->severity ?? 'mineure') {
                                    'critique' => 'severity-critical',
                                    'majeure' => 'severity-major',
                                    default => 'severity-minor'
                                };
                                $severityLabel = match($accident->severity ?? 'mineure') {
                                    'critique' => '🔴 Critique',
                                    'majeure' => '🟠 Majeure',
                                    default => '🟡 Mineure'
                                };
                            @endphp
                            <span class="severity-badge {{ $severityClass }}">{{ $severityLabel }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Responsabilité:</td>
                        <td>
                            @switch($accident->responsibility ?? 'non_determine')
                                @case('driver')
                                    Conducteur responsable
                                    @break
                                @case('third_party')
                                    Tiers responsable
                                    @break
                                @case('shared')
                                    Responsabilité partagée
                                    @break
                                @default
                                    Non déterminé
                            @endswitch
                        </td>
                    </tr>
                    <tr>
                        <td>Coût Estimé:</td>
                        <td><strong>{{ number_format($accident->estimated_cost ?? 0, 2) }} DH</strong></td>
                    </tr>
                </table>

                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                    <strong>Description:</strong><br>
                    <p style="margin: 10px 0;">{{ $accident->description ?? 'Aucune description fournie' }}</p>
                </div>

                @if($accident->injuries ?? false)
                <div style="margin-top: 15px; padding: 10px; background: #ffe3e3; border-radius: 5px;">
                    <strong>⚠️ Blessures Signalées</strong><br>
                    {{ $accident->injury_description ?? 'Détails non fournis' }}
                </div>
                @endif
            </div>

            <div class="checklist">
                <strong>✅ Actions Immédiates Requises:</strong>
                <ul style="margin: 10px 0;">
                    <li>Contacter l'assureur immédiatement</li>
                    <li>Prendre des photos du véhicule et du lieu</li>
                    <li>Remplir le constat amiable</li>
                    <li>Obtenir les coordonnées des témoins</li>
                    <li>Déposer une déclaration à la police si nécessaire</li>
                    <li>Vérifier l'état du conducteur</li>
                    <li>Organiser le remorquage si nécessaire</li>
                </ul>
            </div>

            <center>
                <a href="{{ config('app.url') }}/accidents/{{ $accident->id }}" class="btn">
                    Voir le Rapport Complet
                </a>
            </center>

            <p style="margin-top: 30px; padding: 15px; background: #e7f5ff; border-left: 4px solid #1c7ed6; border-radius: 4px;">
                <strong>📋 Rappel:</strong> Assurez-vous que tous les documents nécessaires sont rassemblés et que la déclaration est faite dans les 5 jours ouvrables auprès de l'assureur.
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
