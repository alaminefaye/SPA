<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket Achat #{{ $purchase->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            width: 80mm; /* Largeur standard pour ticket thermique */
        }
        .ticket {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .logo {
            max-width: 30%;
            height: auto;
            margin: 0 auto;
            display: block;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
            text-align: center;
        }
        .subtitle {
            font-size: 12px;
            text-align: center;
            margin-bottom: 10px;
        }
        .info-block {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        .points {
            text-align: center;
            margin: 10px 0;
            padding: 5px;
            border: 1px solid #000;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .product-table th {
            border-bottom: 1px solid #000;
            padding: 3px;
            text-align: left;
        }
        .product-table td {
            padding: 3px;
            border-bottom: 1px dotted #ccc;
        }
        .product-table .quantity {
            text-align: center;
            width: 15%;
        }
        .product-table .price {
            text-align: right;
            width: 25%;
        }
        .product-table .subtotal {
            text-align: right;
            width: 25%;
        }
        .total-row {
            font-weight: bold;
            font-size: 12px;
            margin-top: 5px;
        }
        @media print {
            @page {
                margin: 0;
                size: 80mm auto; /* width height */
            }
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('assets/img/logo/logo.jpeg') }}" class="logo" alt="Jared Spa Logo">
            <div class="title">JARED SPA</div>
            <div class="subtitle">Ticket d'Achat</div>
        </div>
        
        <div class="info-block">
            <div class="info-row">
                <span>N° Achat:</span>
                <span class="text-bold">#{{ $purchase->id }}</span>
            </div>
            <div class="info-row">
                <span>Date:</span>
                <span>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span>Heure:</span>
                <span>{{ \Carbon\Carbon::parse($purchase->created_at)->format('H:i') }}</span>
            </div>
        </div>
        
        <div class="info-block">
            <div class="info-row">
                <span>Client:</span>
                <span class="text-bold">{{ $purchase->client->nom_complet }}</span>
            </div>
            <div class="info-row">
                <span>Téléphone:</span>
                <span>{{ $purchase->client->numero_telephone }}</span>
            </div>
        </div>
        
        <div class="info-block">
            <div class="text-bold" style="margin-bottom: 5px;">Produits:</div>
            
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th class="quantity">Qté</th>
                        <th class="price">Prix</th>
                        <th class="subtotal">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="quantity">{{ $item->quantity }}</td>
                        <td class="price">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                        <td class="subtotal">{{ number_format($item->subtotal, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="info-row total-row">
                <span>TOTAL:</span>
                <span>{{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA</span>
            </div>
            
            <div class="info-row">
                <span>Mode de paiement:</span>
                <span>
                    @if($purchase->payment_method == 'cash')
                        Espèces
                    @elseif($purchase->payment_method == 'wave')
                        Wave
                    @elseif($purchase->payment_method == 'orange_money')
                        Orange Money
                    @else
                        {{ $purchase->payment_method }}
                    @endif
                </span>
            </div>
        </div>
        
        @if($purchase->notes)
        <div class="info-block">
            <div class="text-bold">Remarques:</div>
            <div>{{ $purchase->notes }}</div>
        </div>
        @endif
        
        <div class="points">
            Points de fidélité gagnés: {{ $pointsGagnes ?? 0 }}<br>
            Total de vos points: {{ $purchase->client->points ?? 0 }}
            @if(isset($purchase->client->points) && $purchase->client->points >= 5)
                <div style="color: #ff0000; margin-top: 5px; font-weight: bold;">
                    Félicitations ! Vous avez gagné une séance gratuite !
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>Merci pour votre achat!</p>
            <p>{{ date('d/m/Y H:i') }}</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Imprimer le ticket</button>
        <a href="{{ route('purchases.index') }}">Retour aux achats</a>
    </div>
</body>
</html>
