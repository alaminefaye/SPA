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
    
    <!-- Section de recherche -->
    <div class="card-body pb-0">
        <form method="GET" action="{{ route('seances.a_demarrer') }}" id="search-form">
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control" placeholder="Rechercher un client, salon ou prestation" name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select" name="salon_id">
                        <option value="">Tous les salons</option>
                        @foreach($salons as $id => $nom)
                            <option value="{{ $id }}" {{ request('salon_id') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select" name="statut">
                        <option value="">Tous les statuts</option>
                        <option value="planifiee" {{ request('statut') == 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-filter-alt me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('seances.a_demarrer') }}" class="btn btn-secondary">
                        <i class="bx bx-reset me-1"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>
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
                        <tr data-id="{{ $seance->id }}" data-statut="{{ $seance->statut }}">
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
<!-- Script de notification sonore -->
<script src="{{ asset('assets/js/notification-sound.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        });
        
        // Variables pour le contrôle du bip
        let beepsPlayed = {};
        let lastBeepTime = {};
        const MAX_BEEPS = 10;
        const BEEP_INTERVAL = 3000; // 3 secondes entre chaque bip en millisecondes
        
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
                
                // Vérifier le statut de la séance pour décider de jouer le bip
                const tr = element.closest('tr');
                const seanceStatus = tr ? tr.getAttribute('data-statut') : null;
                
                // Jouer le bip seulement si la séance est en cours et pas terminée
                if (seanceStatus === 'en_cours') {
                    // Utiliser la nouvelle fonction de bips répétés si disponible
                    if (typeof window.playRepeatedBeep === 'function') {
                        // Cette fonction gère elle-même la répétition et le comptage
                        window.playRepeatedBeep(seanceId);
                    } 
                    // Fallback à l'ancienne méthode
                    else if (beepsPlayed[seanceId] < MAX_BEEPS) {
                        const currentTime = Date.now();
                        const canPlayBeep = !lastBeepTime[seanceId] || (currentTime - lastBeepTime[seanceId] >= BEEP_INTERVAL);
                        
                        if (canPlayBeep && typeof window.playNotificationBeep === 'function') {
                            window.playNotificationBeep();
                            beepsPlayed[seanceId]++;
                            lastBeepTime[seanceId] = currentTime;
                            console.log(`Bip joué pour séance ${seanceId} - ${beepsPlayed[seanceId]}/${MAX_BEEPS} à ${new Date().toLocaleTimeString()}`);
                        }
                    }
                }
                // Si la séance est terminée, arrêter les bips
                else if (seanceStatus === 'termine' || seanceStatus === 'terminee') {
                    if (typeof window.stopRepeatedBeep === 'function') {
                        window.stopRepeatedBeep(seanceId);
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
                            
                            // Déclencher immédiatement la vérification des séances en cours
                            if (window.notificationManager && typeof window.notificationManager.checkSeancesImmediate === 'function') {
                                console.log('Déclenchement immédiat de la vérification des séances après démarrage');
                                window.notificationManager.checkSeancesImmediate();
                            }
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
                            // Mise à jour du badge de statut (4e colonne - index 4)
                            const statusBadge = document.querySelector(`tr[data-id="${seanceId}"] td:nth-child(4) span.badge`);
                            if (statusBadge) {
                                statusBadge.className = 'badge bg-warning';
                                statusBadge.textContent = 'En cours';
                            }
                            
                            // Mise à jour du statut de la séance pour le système de notifications
                            const tr = document.querySelector(`tr[data-id="${seanceId}"]`);
                            if (tr) {
                                tr.setAttribute('data-statut', 'en_cours');
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
                    const tr = document.querySelector(`tr[data-id="${seanceId}"]`);
                    
                    // Ajout d'une classe visuelle pour montrer que la séance est en train d'être terminée
                    if (tr) tr.classList.add('terminating');
                    
                    // Désactiver le bouton pour éviter les clics multiples
                    this.disabled = true;
                    this.innerHTML = '<i class="bx bx-loader bx-spin me-1"></i> Traitement...';
                    
                    // Arrêter les bips sonores si actifs
                    if (window.stopRepeatedBeep && typeof window.stopRepeatedBeep === 'function') {
                        window.stopRepeatedBeep(seanceId);
                    }
                    
                    // Appel AJAX pour terminer la séance avec une approche plus robuste
                    // On crée un FormData pour une requête multipart standard
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    fetch(`/seances/${seanceId}/terminer`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        // Déboguer la réponse brute pour voir ce qui est reçu réellement
                        return response.text().then(text => {
                            console.log('Réponse brute:', text);
                            try {
                                // Tenter de parser le JSON
                                const data = JSON.parse(text);
                                return data;
                            } catch (e) {
                                // Si ce n'est pas du JSON, lancer une erreur avec le début du texte
                                console.error('Erreur de parsing JSON:', e);
                                console.error('Texte reçu:', text.substring(0, 100));
                                throw new Error(`Erreur de parsing: ${text.substring(0, 20)}...`);
                            }
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            // Arrêter tous les bips sonores pour cette séance
                            if (window.stopRepeatedBeep && typeof window.stopRepeatedBeep === 'function') {
                                window.stopRepeatedBeep(seanceId);
                            }
                            // Rediriger vers la page détaillée de la séance
                            window.location.href = `/seances/${seanceId}`;
                        } else {
                            if (tr) tr.classList.remove('terminating');
                            this.disabled = false;
                            this.innerHTML = '<i class="bx bx-check-circle me-1"></i> Terminer';
                            alert('Erreur lors de la fin de la séance: ' + data.message);
                        }
                    })
                    .catch(error => {
                        // Réactiver le bouton en cas d'erreur
                        if (tr) tr.classList.remove('terminating');
                        this.disabled = false;
                        this.innerHTML = '<i class="bx bx-check-circle me-1"></i> Terminer';
                        
                        // Afficher des informations détaillées sur l'erreur
                        console.error('Erreur détaillée:', error);
                        
                        // Créer un message d'erreur détaillé
                        let errorMsg = 'Erreur lors de la fin de la séance: ' + error.message;
                        
                        // Créer une div pour afficher l'erreur (pour le débogage)
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'alert alert-danger mt-3 error-debug';
                        errorDiv.innerHTML = `<h5>Erreur détaillée (debug)</h5>
                            <p>Séance ID: ${seanceId}</p>
                            <p>Message: ${error.message}</p>
                            <p>Statut: ${tr ? tr.getAttribute('data-statut') : 'inconnu'}</p>`;
                        
                        // Ajouter la div d'erreur après le tableau ou avant le footer
                        const tableContainer = document.querySelector('.table-responsive');
                        if (tableContainer) {
                            tableContainer.parentNode.insertBefore(errorDiv, tableContainer.nextSibling);
                        }
                        
                        // Afficher l'alerte
                        alert(errorMsg);
                    });
                }
            });
        });
    });
</script>
@endsection
