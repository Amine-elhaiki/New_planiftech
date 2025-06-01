<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports d'intervention</title>
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
        .report {
            margin-bottom: 40px;
            page-break-after: always;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .report:last-child {
            page-break-after: auto;
        }
        .report-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin: -20px -20px 20px -20px;
            border-bottom: 2px solid #007bff;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .report-meta {
            font-size: 14px;
            color: #666;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-label, .info-value {
            display: table-cell;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            border-bottom: 1px solid #007bff;
            padding-bottom: 3px;
            margin-bottom: 10px;
        }
        .content {
            text-align: justify;
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 3px solid #007bff;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .summary {
            background-color: #e9ecef;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ORMVAT - PlanifTech</div>
        <div>Office Régional de Mise en Valeur Agricole du Tadla</div>
        <div class="title">COMPILATION DE RAPPORTS D'INTERVENTION</div>
    </div>

    <div class="summary">
        <div class="summary-title">RÉSUMÉ</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre de rapports :</div>
                <div class="info-value">{{ $reports->count() }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Période :</div>
                <div class="info-value">
                    Du {{ $reports->min('date_intervention')?->format('d/m/Y') }}
                    au {{ $reports->max('date_intervention')?->format('d/m/Y') }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Types d'intervention :</div>
                <div class="info-value">{{ $reports->pluck('type_intervention')->unique()->implode(', ') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Intervenants :</div>
                <div class="info-value">{{ $reports->pluck('utilisateur.nom_complet')->unique()->implode(', ') }}</div>
            </div>
        </div>
    </div>

    @foreach($reports as $index => $report)
    <div class="report">
        <div class="report-header">
            <div class="report-title">{{ $report->titre }}</div>
            <div class="report-meta">
                Rapport #{{ $report->id }} - {{ $report->date_intervention->format('d/m/Y') }} - {{ $report->utilisateur->nom_complet }}
            </div>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Lieu :</div>
                <div class="info-value">{{ $report->lieu }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Type :</div>
                <div class="info-value">{{ $report->type_intervention_libelle }}</div>
            </div>
            @if($report->tache)
            <div class="info-row">
                <div class="info-label">Tâche :</div>
                <div class="info-value">{{ $report->tache->titre }}</div>
            </div>
            @endif
            @if($report->evenement)
            <div class="info-row">
                <div class="info-label">Événement :</div>
                <div class="info-value">{{ $report->evenement->titre }}</div>
            </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">ACTIONS RÉALISÉES</div>
            <div class="content">{{ $report->actions }}</div>
        </div>

        <div class="section">
            <div class="section-title">RÉSULTATS OBTENUS</div>
            <div class="content">{{ $report->resultats }}</div>
        </div>

        @if($report->problemes)
        <div class="section">
            <div class="section-title">PROBLÈMES RENCONTRÉS</div>
            <div class="content">{{ $report->problemes }}</div>
        </div>
        @endif

        @if($report->recommandations)
        <div class="section">
            <div class="section-title">RECOMMANDATIONS</div>
            <div class="content">{{ $report->recommandations }}</div>
        </div>
        @endif
    </div>
    @endforeach

    <div class="footer">
        <p>Compilation générée le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>ORMVAT - Office Régional de Mise en Valeur Agricole du Tadla</p>
        <p>Système PlanifTech - Gestion des interventions techniques</p>
    </div>
</body>
</html>
