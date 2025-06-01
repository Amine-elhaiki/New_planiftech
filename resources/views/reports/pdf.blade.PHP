<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport d'intervention - {{ $report->titre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            margin: 20px 0;
            color: #333;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-row {
            display: table-row;
        }
        .info-label, .info-value {
            display: table-cell;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
            color: #555;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            border-bottom: 1px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .content {
            text-align: justify;
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ORMVAT - PlanifTech</div>
        <div>Office Régional de Mise en Valeur Agricole du Tadla</div>
        <div class="title">RAPPORT D'INTERVENTION</div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Titre :</div>
            <div class="info-value">{{ $report->titre }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date d'intervention :</div>
            <div class="info-value">{{ $report->date_intervention->format('d/m/Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Lieu :</div>
            <div class="info-value">{{ $report->lieu }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Type d'intervention :</div>
            <div class="info-value">{{ $report->type_intervention_libelle }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Intervenant :</div>
            <div class="info-value">{{ $report->utilisateur->nom_complet }}</div>
        </div>
        @if($report->tache)
        <div class="info-row">
            <div class="info-label">Tâche associée :</div>
            <div class="info-value">{{ $report->tache->titre }}</div>
        </div>
        @endif
        @if($report->evenement)
        <div class="info-row">
            <div class="info-label">Événement associé :</div>
            <div class="info-value">{{ $report->evenement->titre }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">ACTIONS RÉALISÉES</div>
        <div class="content">
            {{ $report->actions }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">RÉSULTATS OBTENUS</div>
        <div class="content">
            {{ $report->resultats }}
        </div>
    </div>

    @if($report->problemes)
    <div class="section">
        <div class="section-title">PROBLÈMES RENCONTRÉS</div>
        <div class="content">
            {{ $report->problemes }}
        </div>
    </div>
    @endif

    @if($report->recommandations)
    <div class="section">
        <div class="section-title">RECOMMANDATIONS</div>
        <div class="content">
            {{ $report->recommandations }}
        </div>
    </div>
    @endif

    @if($report->piecesJointes && $report->piecesJointes->count() > 0)
    <div class="section">
        <div class="section-title">PIÈCES JOINTES</div>
        <ul>
            @foreach($report->piecesJointes as $piece)
            <li>{{ $piece->nom_fichier }} ({{ $piece->taille_formattee }})</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>ORMVAT - Office Régional de Mise en Valeur Agricole du Tadla</p>
        <p>Système PlanifTech - Gestion des interventions techniques</p>
    </div>
</body>
</html>
