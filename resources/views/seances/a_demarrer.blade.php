@extends('layouts.app')

@section('title', 'Séances à démarrer')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / Séances /</span> À démarrer
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Séances du jour à démarrer</h5>
        <a href="{{ route('seances.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Nouvelle Séance
        </a>
    </div>
    
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Salon</th>
                        <th>Prestation</th>
                        <th>Statut</th>
                        <th>Début (prévu)</th>
                        <th>Fin (prévue)</th>
                        <th>Temps restant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($seances as $seance)
                        <tr data-id="{{ $seance->id }}">
                            <td>{{ $seance->client->nom_complet }}</td>
                            <td>{{ $seance->salon->nom }}</td>
                            <td>
                                @if($seance->prestations->count() > 0)
                                    @if($seance->prestations->count() == 1)
                                        {{ $seance->prestations->first()->nom_prestation }}
                                    @else
                                        <span class="badge bg-info">{{ $seance->prestations->count() }} prestations</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary popover-btn" data-bs-toggle="popover" title="Prestations" 
                                            data-bs-content="{{ $seance->prestations->pluck('nom_prestation')->join(', ') }}">
                                            <i class='bx bx-info-circle'></i>
                                        </button>
                                    @endif
                                @else
                                    <span class="text-muted">Aucune prestation</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">Planifiée</span>
                            </td>
                            <td>00:00</td>
                            <td>
                                @php
                                $dureeMinutes = 0;
                                foreach ($seance->prestations as $prestation) {
                                    $dureeStr = is_object($prestation->duree) ? $prestation->duree->format('H:i:s') : $prestation->duree;
                                    $dureeParts = explode(':', $dureeStr);
                                    $dureeMinutes += $dureeParts[0] * 60 + $dureeParts[1];
                                }
                                $heures = floor($dureeMinutes / 60);
                                $minutes = $dureeMinutes % 60;
                                @endphp
                                {{ sprintf('%02d:%02d', $heures, $minutes) }}
                            </td>
                            <td>
                                @php
                                $dureeMinutes = 0;
                                foreach ($seance->prestations as $prestation) {
                                    $dureeStr = is_object($prestation->duree) ? $prestation->duree->format('H:i:s') : $prestation->duree;
                                    $dureeParts = explode(':', $dureeStr);
                                    $dureeMinutes += $dureeParts[0] * 60 + $dureeParts[1];
                                }
                                @endphp
                                @if($seance->statut === 'en_cours')
                                <span class="countdown running" 
                                      data-debut="{{ $seance->heure_debut ? (is_object($seance->heure_debut) ? $seance->heure_debut->format('Y-m-d H:i:s') : $seance->heure_debut) : '' }}" 
                                      data-duree-minutes="{{ $dureeMinutes }}"
                                      data-id="{{ $seance->id }}">
                                    Calcul...
                                </span>
                                @else
                                <span class="countdown" 
                                      data-duree-minutes="{{ $dureeMinutes }}"
                                      data-id="{{ $seance->id }}">
                                    -
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($seance->statut === 'planifiee')
                                        <button type="button" class="dropdown-item btn-demarrer" data-id="{{ $seance->id }}">
                                            <i class="bx bx-play-circle me-1"></i> Démarrer
                                        </button>
                                        @elseif($seance->statut === 'en_cours')
                                        <button type="button" class="dropdown-item btn-terminer" data-id="{{ $seance->id }}">
                                            <i class="bx bx-stop-circle me-1"></i> Terminer
                                        </button>
                                        @else
                                        <button type="button" class="dropdown-item" disabled>
                                            <i class="bx bx-check-circle me-1"></i> Séance terminée
                                        </button>
                                        @endif
                                        <a class="dropdown-item" href="{{ route('seances.show', $seance->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> Voir détails
                                        </a>
                                        <a class="dropdown-item" href="{{ route('seances.edit', $seance->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <form action="{{ route('seances.destroy', $seance->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')">
                                                <i class="bx bx-trash me-1"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucune séance à démarrer aujourd'hui</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        });
        
        // Variables pour le contrôle du bip
        let beepsPlayed = {};
        const MAX_BEEPS = 10;
        
        // Initialiser les compteurs à rebours
        const countdowns = document.querySelectorAll('.countdown.running');
        countdowns.forEach(function(countdown) {
            updateCountdown(countdown);
        });
        
        // Mettre à jour les compteurs toutes les secondes
        setInterval(function() {
            document.querySelectorAll('.countdown.running').forEach(function(countdown) {
                updateCountdown(countdown);
            });
        }, 1000);
        
        // Fonction pour calculer et afficher le temps restant
        function updateCountdown(element) {
            // Si l'élément n'a pas la classe 'running', ne pas démarrer le compteur
            if (!element.classList.contains('running')) {
                // Assurons-nous que les éléments non-running affichent toujours un tiret
                if (element.textContent.trim() !== '-') {
                    element.innerHTML = '-';
                }
                return;
            }
            
            const seanceId = element.getAttribute('data-id');
            const debutStr = element.getAttribute('data-debut');
            
            // Si pas de date de début (heure_debut), ne pas démarrer le compteur
            if (!debutStr) {
                element.innerHTML = '-';
                return;
            }
            
            const debutDate = new Date(debutStr);
            const now = new Date();
            
            // Calculer la différence en secondes
            const dureeMinutes = parseInt(element.getAttribute('data-duree-minutes') || '0');
            const dureeSeconds = dureeMinutes * 60;
            const tempsEcouleSeconds = Math.floor((now - debutDate) / 1000);
            let tempsRestant = dureeSeconds - tempsEcouleSeconds;
            
            // Initialiser le compteur de bips si nécessaire
            if (beepsPlayed[seanceId] === undefined) {
                beepsPlayed[seanceId] = 0;
            }
            
            // Formater le temps restant
            if (tempsRestant <= 0) {
                // Le temps est dépassé
                const depassementSeconds = Math.abs(tempsRestant);
                const hours = Math.floor(depassementSeconds / 3600);
                const minutes = Math.floor((depassementSeconds % 3600) / 60);
                const seconds = depassementSeconds % 60;
                
                element.innerHTML = `<span class="text-danger">Retard: ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}</span>`;
                
                // Jouer le bip si le temps vient juste d'être dépassé et si on n'a pas joué MAX_BEEPS fois
                if (beepsPlayed[seanceId] < MAX_BEEPS && Math.abs(tempsRestant) < 10) {
                    if (typeof window.playNotificationBeep === 'function') {
                        window.playNotificationBeep();
                        beepsPlayed[seanceId]++;
                        console.log(`Bip joué pour séance ${seanceId} - ${beepsPlayed[seanceId]}/${MAX_BEEPS}`);
                    }
                }
            } else {
                // Il reste du temps
                const hours = Math.floor(tempsRestant / 3600);
                const minutes = Math.floor((tempsRestant % 3600) / 60);
                const seconds = tempsRestant % 60;
                
                element.innerHTML = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }
        
        // Démarrer une séance
        const btnDemarrer = document.querySelectorAll('.btn-demarrer');
        btnDemarrer.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const seanceId = this.getAttribute('data-id');
                
                // Désactiver le bouton pour éviter les clics multiples
                this.disabled = true;
                this.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> En cours...';
                
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
                        // Activer le minuteur pour cette séance
                        const countdownElement = document.querySelector(`.countdown[data-id="${seanceId}"]`);
                        if (countdownElement) {
                            countdownElement.classList.add('running');
                            countdownElement.setAttribute('data-debut', new Date().toISOString());
                            updateCountdown(countdownElement);
                        }
                        
                        // Remplacer le bouton Démarrer par Terminer sans recharger la page
                        const dropdownMenu = this.closest('.dropdown-menu');
                        if (dropdownMenu) {
                            const newButton = document.createElement('button');
                            newButton.type = 'button';
                            newButton.className = 'dropdown-item btn-terminer';
                            newButton.setAttribute('data-id', seanceId);
                            newButton.innerHTML = '<i class="bx bx-stop-circle me-1"></i> Terminer';
                            
                            // Ajouter l'écouteur d'événements pour le nouveau bouton
                            newButton.addEventListener('click', function() {
                                if (confirm('Êtes-vous sûr de vouloir terminer cette séance ?')) {
                                    const seanceId = this.getAttribute('data-id');
                                    
                                    // Désactiver le bouton et afficher un indicateur de chargement
                                    this.disabled = true;
                                    this.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> En cours...';
                                    
                                    // Récupérer le jeton CSRF depuis la balise meta
                                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                    
                                    fetch(`/seances/${seanceId}/terminer`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({}) // Corps vide mais nécessaire pour certains frameworks
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            window.location.href = `/seances/${seanceId}`;
                                        } else {
                                            alert('Erreur lors de la fin de la séance: ' + data.message);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Erreur:', error);
                                        alert('Erreur lors de la fin de la séance');
                                    });
                                }
                            });
                            
                            // Remplacer l'ancien bouton par le nouveau
                            this.parentNode.replaceChild(newButton, this);
                            
                            // Mettre à jour le badge de statut
                            const statusBadge = document.querySelector(`tr[data-id="${seanceId}"] td:nth-child(5) span.badge`);
                            if (statusBadge) {
                                statusBadge.className = 'badge bg-warning';
                                statusBadge.textContent = 'En cours';
                            }
                        }
                    } else {
                        // Réactiver le bouton en cas d'erreur
                        this.disabled = false;
                        this.innerHTML = '<i class="bx bx-play-circle me-1"></i> Démarrer';
                        alert('Erreur lors du démarrage de la séance: ' + data.message);
                    }
                })
                .catch(error => {
                    // Réactiver le bouton en cas d'erreur
                    this.disabled = false;
                    this.innerHTML = '<i class="bx bx-play-circle me-1"></i> Démarrer';
                    console.error('Erreur:', error);
                    alert('Erreur lors du démarrage de la séance');
                });
            });
        });
        
        // Terminer une séance
        const btnTerminer = document.querySelectorAll('.btn-terminer');
        btnTerminer.forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir terminer cette séance ?')) {
                    const seanceId = this.getAttribute('data-id');
                    
                    // Appel AJAX pour terminer la séance
                    fetch(`/seances/${seanceId}/terminer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Rediriger vers la page détaillée de la séance
                            window.location.href = `/seances/${seanceId}`;
                        } else {
                            alert('Erreur lors de la fin de la séance: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la fin de la séance');
                    });
                }
            });
        });
    });
</script>
@endsection
