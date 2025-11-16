<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiration Document</title>
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
            background: linear-gradient(135deg, #ffa94d 0%, #fd7e14 100%);
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
            border-left: 4px solid #ffa94d;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .document-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .document-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .document-info td {
            padding: 8px 0;
        }
        .document-info td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #ffa94d;
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
            <div class="icon">📄</div>
            <h1>Document Expirant</h1>
            <p>Rappel de Renouvellement</p>
        </div>

        <div class="content">
            <p>Bonjour,</p>

            <div class="alert-box">
                <strong>⚠️ Document Arrivant à Expiration</strong><br>
                Un document important arrive à expiration. Veuillez procéder au renouvellement.
            </div>

            <div class="document-info">
                <table>
                    <tr>
                        <td>Type Document:</td>
                        <td><strong>{{ $document->document_type ?? 'Document Véhicule' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Véhicule:</td>
                        <td>{{ $document->vehicle->registration_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>N° Document:</td>
                        <td>{{ $document->document_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Date Émission:</td>
                        <td>{{ isset($document->issue_date) ? \Carbon\Carbon::parse($document->issue_date)->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Date Expiration:</td>
                        <td><strong style="color: #fd7e14;">
                            {{ isset($document->expiration_date) ? \Carbon\Carbon::parse($document->expiration_date)->format('d/m/Y') : 'N/A' }}
                        </strong></td>
                    </tr>
                    <tr>
                        <td>Jours Restants:</td>
                        <td><strong>
                            {{ isset($document->expiration_date) ? \Carbon\Carbon::parse($document->expiration_date)->diffInDays(now()) : '0' }} jours
                        </strong></td>
                    </tr>
                </table>
            </div>

            <p><strong>Documents généralement requis pour le renouvellement:</strong></p>
            <ul>
                <li>Carte grise du véhicule</li>
                <li>Attestation d'assurance en cours</li>
                <li>Contrôle technique valide (si applicable)</li>
                <li>Pièce d'identité du propriétaire</li>
                <li>Justificatif de domicile</li>
            </ul>

            <center>
                <a href="{{ config('app.url') }}/vehicle-documents/{{ $document->id ?? '' }}/edit" class="btn">
                    Mettre à Jour le Document
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
