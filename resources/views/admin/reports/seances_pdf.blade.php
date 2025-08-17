<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des séances</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #fafafa;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
            position: relative;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 10px;
            display: block;
            margin: 0 auto 10px auto;
        }
        .header h1 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .date-range {
            color: #4a5568;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .stat-box {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 15px 20px;
            width: 22%;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #4299e1;
        }
        .stat-box h3 {
            color: #4a5568;
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
        }
        .stat-box p {
            font-size: 22px;
            font-weight: bold;
            color: #2d3748;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #4299e1;
            font-weight: bold;
            color: white;
            border-top: none;
            border-bottom: 2px solid #2b6cb0;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover {
            background-color: #ebf4ff;
        }
        h2 {
            color: #2d3748;
            border-bottom: 2px solid #4299e1;
            padding-bottom: 10px;
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 20px;
            position: relative;
        }
        h2:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background-color: #3182ce;
        }
        .status-terminee {
            color: #38a169;
            font-weight: bold;
        }
        .status-annulee {
            color: #e53e3e;
            font-weight: bold;
        }
        .status-en_attente {
            color: #d69e2e;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            color: #4a5568;
            font-size: 12px;
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            font-style: italic;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('assets/img/logo/jared-spa-logo.png') }}" class="logo" alt="Jared SPA Logo">
        <h1>{{ $reportTitle }}</h1>
        <p class="date-range">Période : {{ $dateRange }}</p>
    </div>

    <!-- Statistiques globales -->
    <table>
        <tr>
            <th>Séances totales</th>
            <th>Séances terminées</th>
            <th>Séances annulées</th>
            <th>Revenu total</th>
        </tr>
        <tr>
            <td>{{ $totalSeances }}</td>
            <td>{{ $totalTerminees }} ({{ $totalSeances > 0 ? round(($totalTerminees / $totalSeances) * 100, 1) : 0 }}%)</td>
            <td>{{ $totalAnnulees }} ({{ $totalSeances > 0 ? round(($totalAnnulees / $totalSeances) * 100, 1) : 0 }}%)</td>
            <td>{{ number_format($totalRevenu, 0, ',', ' ') }} fr</td>
        </tr>
    </table>

    <!-- Statistiques par salon -->
    <h2>Statistiques par salon</h2>
    <table>
        <thead>
            <tr>
                <th>Salon</th>
                <th>Total séances</th>
                <th>Terminées</th>
                <th>Annulées</th>
                <th>Revenu généré</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seancesBySalon as $salonStats)
            <tr>
                <td>{{ $salonStats['nom'] }}</td>
                <td>{{ $salonStats['total'] }}</td>
                <td>{{ $salonStats['terminees'] }}</td>
                <td>{{ $salonStats['annulees'] }}</td>
                <td>{{ number_format($salonStats['revenu'], 0, ',', ' ') }} fr</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Liste des séances -->
    <h2>Liste détaillée des séances</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Client</th>
                <th>Salon</th>
                <th>Prestations</th>
                <th>Prix</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seances as $seance)
            <tr>
                <td>{{ $seance->id }}</td>
                <td>{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</td>
                <td>{{ $seance->heure_prevu }}</td>
                <td>{{ $seance->client ? $seance->client->nom_complet : 'Client supprimé' }}</td>
                <td>{{ $seance->salon ? $seance->salon->nom : 'Salon supprimé' }}</td>
                <td>
                    @foreach($seance->prestations as $prestation)
                        {{ $prestation->nom_prestation }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </td>
                <td>{{ number_format($seance->prix, 0, ',', ' ') }} fr</td>
                <td class="status-{{ $seance->statut }}">
                    {{ $seance->statut == 'terminee' ? 'Terminée' : ($seance->statut == 'annulee' ? 'Annulée' : 'En attente') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Rapport généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
