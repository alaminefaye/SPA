<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket Séance #{{ $seance->numero_seance }}</title>
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
            margin-bottom: 5px;
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
            margin-bottom: 5px;
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
        .qr-code {
            text-align: center;
            margin: 15px 0;
        }
        .qr-code img {
            max-width: 100px;
            height: auto;
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
            <div class="subtitle">Ticket de Séance</div>
        </div>
        
        <div class="info-block">
            <div class="info-row">
                <span>N° Séance:</span>
                <span class="text-bold">{{ $seance->numero_seance }}</span>
            </div>
            <div class="info-row">
                <span>Date:</span>
                <span>{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span>Heure prévue:</span>
                <span>{{ $seance->heure_prevu }}</span>
            </div>
        </div>
        
        <div class="info-block">
            <div class="info-row">
                <span>Client:</span>
                <span class="text-bold">{{ $seance->client->nom_complet }}</span>
            </div>
            <div class="info-row">
                <span>Téléphone:</span>
                <span>{{ $seance->client->numero_telephone }}</span>
            </div>
        </div>
        
        <div class="info-block">
            <div class="text-bold" style="margin-bottom: 5px;">Prestation(s):</div>
            @forelse($seance->prestations as $prestation)
                <div class="info-row">
                    <span>- {{ $prestation->nom_prestation }}</span>
                    <span>{{ \Carbon\Carbon::parse($prestation->duree)->format('H:i') }}</span>
                </div>
            @empty
                <div class="info-row">
                    <span>Prestation:</span>
                    <span>Non définie</span>
                </div>
            @endforelse
            @if($seance->prestations->count() > 1)
                <div class="info-row" style="margin-top: 5px;">
                    <span>Durée totale:</span>
                    <span class="text-bold">{{ \Carbon\Carbon::parse($seance->calculerDureeTotale())->format('H:i') }}</span>
                </div>
            @endif
            <div class="info-row">
                <span>Salon:</span>
                <span>{{ $seance->salon->nom }}</span>
            </div>
            
            @if($seance->paid_with_points)
                <div class="info-row">
                    <span>Prix:</span>
                    <span class="text-bold" style="color: #008000;">PAYÉ AVEC POINTS DE FIDÉLITÉ</span>
                </div>
            @elseif($seance->is_free)
                <div class="info-row">
                    <span>Prix:</span>
                    <span class="text-bold">GRATUIT</span>
                </div>
            @else
                <div class="info-row">
                    <span>Prix:</span>
                    @if($seance->prix_promo)
                        <span>
                            <span style="text-decoration: line-through;">{{ number_format($seance->prix, 0, ',', ' ') }} FCFA</span>
                            <span class="text-bold" style="color: #ff0000;">{{ number_format($seance->prix_promo, 0, ',', ' ') }} FCFA</span>
                        </span>
                    @else
                        <span class="text-bold">{{ number_format($seance->prix, 0, ',', ' ') }} FCFA</span>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- @if($seance->commentaire)
        <div class="info-block">
            <div class="text-bold">Commentaire:</div>
            <div>{{ $seance->commentaire }}</div>
        </div>
        @endif -->
        
        <div class="points">
            @if($seance->paid_with_points)
                <div style="color: #008000; margin-bottom: 5px; font-weight: bold;">
                    Vous avez utilisé vos points de fidélité pour payer cette séance.
                </div>
            @endif
            Points de fidélité gagnés: {{ $pointsGagnes }}<br>
            Total de vos points: {{ $pointsTotal + $pointsGagnes }}
            @if(($pointsTotal + $pointsGagnes) >= 5 && !$seance->paid_with_points)
                <div style="color: #ff0000; margin-top: 5px; font-weight: bold;">
                    Félicitations ! Vous avez gagné une séance gratuite !
                </div>
            @endif
        </div>
        
        <div class="qr-code">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
            <div class="text-center">Scannez pour démarrer la séance</div>
        </div>
        
        <div class="footer">
            <p>Merci pour votre confiance!</p>
            <p>{{ date('d/m/Y H:i') }}</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Imprimer le ticket</button>
        <a href="{{ route('seances.index') }}">Retour aux séances</a>
    </div>
</body>
</html>
