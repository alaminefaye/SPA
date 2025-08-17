<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
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
            width: 28%;
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
        .footer {
            text-align: center;
            color: #4a5568;
            font-size: 12px;
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            font-style: italic;
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
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('assets/img/logo/jared-spa-logo.png') }}" class="logo" alt="Jared SPA Logo">
        <h1>{{ $reportTitle }}</h1>
        <p class="date-range">Période: {{ $dateRange }}</p>
    </div>

    <div class="stats-container">
        <div class="stat-box">
            <h3>Total des ventes</h3>
            <p>{{ $totalSales }}</p>
        </div>
        <div class="stat-box">
            <h3>Revenu total</h3>
            <p>{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="stat-box">
            <h3>Produits vendus</h3>
            <p>{{ $totalProducts }}</p>
        </div>
    </div>

    <h2>Produits par ventes</h2>
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité vendue</th>
                <th>Revenu généré</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productStats as $product)
            <tr>
                <td>{{ $product['name'] }}</td>
                <td>{{ $product['quantity'] }}</td>
                <td>{{ number_format($product['revenue'], 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Détails des ventes</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Produits</th>
                <th>Montant</th>
                <th>Mode de paiement</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
            <tr>
                <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $purchase->client ? $purchase->client->nom_complet : 'Client anonyme' }}</td>
                <td>
                    @foreach($purchase->products as $index => $product)
                        {{ $product->name }} (x{{ $product->pivot->quantity }}){{ $index < count($purchase->products) - 1 ? ', ' : '' }}
                    @endforeach
                </td>
                <td>{{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA</td>
                <td>{{ $purchase->payment_method }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Rapport généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
