@extends('layouts.app')

@section('title', 'Détails de la Séance')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('seances.index') }}">Séances</a> /</span> Détails
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Détails de la Séance #{{ $seance->id }}</h5>
                <div>
                    <a href="{{ route('seances.ticket', $seance->id) }}" class="btn btn-info btn-sm me-2" target="_blank">
                        <i class="bx bx-printer me-1"></i> Imprimer Ticket
                    </a>
                    <a href="{{ route('seances.edit', $seance->id) }}" class="btn btn-primary btn-sm me-2">
                        <i class="bx bx-edit-alt me-1"></i> Modifier
                    </a>
                    <a href="{{ route('seances.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-header d-flex justify-content-center bg-light py-3 mt-2">
                <div id="timer-controls" class="d-flex gap-3">
                    @if($seance->statut == 'planifiee')
                        <button type="button" id="btn-demarrer" class="btn btn-success" data-id="{{ $seance->id }}">
                            <i class="bx bx-play-circle me-1"></i> Démarrer la séance
                        </button>
                    @elseif($seance->statut == 'en_cours')
                        <div class="d-flex flex-column align-items-center me-4">
                            <span class="mb-2">Temps écoulé:</span>
                            <div id="timer" class="fs-3 fw-bold">00:00:00</div>
                            <div id="timer-alert" class="mt-1 small text-danger"></div>
                        </div>
                        <form action="{{ route('seances.terminer', $seance->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bx bx-stop-circle me-1"></i> Terminer la séance
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informations Client</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 35%">Nom Complet:</th>
                                    <td>{{ $seance->client->nom_complet }}</td>
                                </tr>
                                <tr>
                                    <th>Numéro Téléphone:</th>
                                    <td>{{ $seance->client->numero_telephone }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $seance->client->adresse_mail }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Informations Séance</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">

                                <tr>
                                    <th>Statut:</th>
                                    <td>
                                        @switch($seance->statut)
                                            @case('planifiee')
                                                <span class="badge bg-primary">Planifiée</span>
                                                @break
                                            @case('en_cours')
                                                <span class="badge bg-warning">En cours</span>
                                                @break
                                            @case('termine')
                                                <span class="badge bg-success">Terminée</span>
                                                @break
                                            @case('annule')
                                                <span class="badge bg-danger">Annulée</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Salon</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 35%">Nom:</th>
                                    <td>{{ $seance->salon->nom }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Prestations</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                @if($seance->prestations->count() > 0)
                                    <tr>
                                        <th style="width: 35%">Prestations:</th>
                                        <td>
                                            <ul class="list-unstyled">
                                                @foreach($seance->prestations as $prestation)
                                                    <li>
                                                        <i class="bx bx-check-circle text-success me-1"></i>
                                                        {{ $prestation->nom_prestation }} 
                                                        <span class="text-muted small">({{ number_format($prestation->prix, 2, ',', ' ') }} FCFA, {{ $prestation->duree->format('H:i') }})</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <th style="width: 35%">Prestations:</th>
                                        <td><span class="text-muted">Aucune prestation</span></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Prix Total:</th>
                                    <td><strong>{{ number_format($seance->prix, 2, ',', ' ') }} FCFA</strong></td>
                                </tr>
                                <tr>
                                    <th>Durée Totale:</th>
                                    <td><strong>{{ $seance->duree->format('H:i') }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                @if($seance->commentaire)
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted">Commentaire</h6>
                        <p>{{ $seance->commentaire }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables pour le timer
        let timer;
        let seconds = 0;
        let totalDurationMinutes = {{ $seance->prestations->sum(function($prestation) {
            $dureeParts = explode(':', $prestation->duree->format('H:i:s'));
            return $dureeParts[0] * 60 + $dureeParts[1];
        }) }};
        let totalDurationSeconds = totalDurationMinutes * 60;
        let alertPlayed = false;
        
        // Si la séance est déjà en cours, calculer le temps écoulé depuis le début
        @if($seance->statut == 'en_cours' && $seance->heure_debut)
            const startTime = new Date('{{ $seance->heure_debut }}').getTime();
            const now = new Date().getTime();
            seconds = Math.floor((now - startTime) / 1000);
            updateTimerDisplay();
            startTimer();
        @endif
        
        // Bouton pour démarrer la séance
        const btnDemarrer = document.getElementById('btn-demarrer');
        if (btnDemarrer) {
            btnDemarrer.addEventListener('click', function() {
                const seanceId = this.getAttribute('data-id');
                
                // Appel AJAX pour démarrer la séance
                fetch(`/seances/${seanceId}/demarrer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page pour afficher le timer
                        window.location.reload();
                    } else {
                        alert('Erreur lors du démarrage de la séance: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du démarrage de la séance');
                });
            });
        }
        
        // Fonction pour démarrer le timer
        function startTimer() {
            timer = setInterval(function() {
                seconds++;
                updateTimerDisplay();
                
                // Vérifier si le temps est dépassé
                if (seconds > totalDurationSeconds && !alertPlayed) {
                    document.getElementById('timer-alert').textContent = 'Attention: Durée dépassée!';
                    document.getElementById('timer').classList.add('text-danger');
                    playAlertSound();
                    alertPlayed = true;
                }
            }, 1000);
        }
        
        // Fonction pour mettre à jour l'affichage du timer
        function updateTimerDisplay() {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            const display = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(secs).padStart(2, '0');
            
            document.getElementById('timer').textContent = display;
        }
        
        // Fonction pour jouer le son d'alerte
        function playAlertSound() {
            if (window.playNotificationBeep) {
                window.playNotificationBeep();
            } else {
                console.log('Fonction de notification non disponible');
            }
        }
    });
</script>
@endsection
