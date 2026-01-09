@extends('layouts.app')

@section('title', 'Créer une Séance')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('seances.index') }}">Séances</a> /</span> Créer
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Nouvelle Séance</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('seances.store') }}" method="POST" id="seanceForm">
                    @csrf
                    
                    <h6 class="mb-3">Informations Client</h6>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="numero_telephone">Numéro Téléphone *</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="numero_telephone" name="numero_telephone" value="{{ old('numero_telephone') }}" placeholder="Numéro de téléphone" required />
                                <button type="button" class="btn btn-primary" id="searchClient">
                                    <i class="bx bx-search"></i> Rechercher
                                </button>
                            </div>
                            <div id="phoneHelp" class="form-text">Saisissez uniquement le numéro de téléphone pour rechercher un client existant</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nom_complet">Nom Complet <span id="nom-required" style="color:red; display:none;">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nom_complet" name="nom_complet" value="{{ old('nom_complet') }}" placeholder="Nom complet du client" />
                            <div class="form-text">Si le client n'existe pas, le nom complet est obligatoire pour le créer automatiquement</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="adresse_mail">Adresse Mail</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="adresse_mail" name="adresse_mail" value="{{ old('adresse_mail') }}" placeholder="Adresse email" />
                            <div class="form-text">Laissez vide si vous recherchez un client existant par téléphone</div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3">Détails de la Séance</h6>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="salon_id">Salon *</label>
                        <div class="col-sm-10">
                            @if($tousSalonsOccupes)
                                <div class="alert alert-warning mb-2">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <strong>Tous les salons sont actuellement occupés</strong>. 
                                    Vous pouvez mettre cette séance en file d'attente jusqu'à ce qu'un salon se libère.
                                </div>
                                <select class="form-select" id="salon_id" name="salon_id" required>
                                    <option value="">Tous les salons sont occupés - Sélectionnez pour file d'attente</option>
                                    @foreach($tousLesSalons as $salon)
                                        <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>
                                            {{ $salon->nom }} (Occupé - Sera mis en file d'attente)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-warning">
                                    <i class="bx bx-time me-1"></i> 
                                    La séance sera automatiquement placée en file d'attente et pourra démarrer dès qu'un salon sera disponible.
                                </div>
                            @else
                                <select class="form-select" id="salon_id" name="salon_id" required>
                                    <option value="">Sélectionnez un salon disponible</option>
                                    @foreach($tousLesSalons as $salon)
                                        @php $estDisponible = in_array($salon->id, $salonsDisponiblesIds); @endphp
                                        <option value="{{ $salon->id }}" 
                                            {{ old('salon_id') == $salon->id ? 'selected' : '' }}
                                            {{ !$estDisponible ? 'disabled' : '' }}>
                                            {{ $salon->nom }} {{ !$estDisponible ? '(Occupé)' : '(Disponible)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($salonsOccupesCount > 0)
                                <div class="form-text text-info">
                                    <i class="bx bx-info-circle me-1"></i> 
                                    {{ $salonsOccupesCount }} salon(s) actuellement occupé(s) et ne peuvent pas être sélectionnés.
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Prestations *</label>
                        <div class="col-sm-10">
                            <div class="alert alert-info mb-2">
                                Sélectionnez une ou plusieurs prestations. Le prix total et la durée totale seront automatiquement calculés.
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" class="form-control" id="recherche-prestation" placeholder="Rechercher une prestation..." autocomplete="off">
                                </div>
                                <div class="form-text">Commencez à taper pour afficher les prestations correspondantes</div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">Sélection</th>
                                            <th>Prestation</th>
                                            <th style="width: 120px;">Prix (FCFA)</th>
                                            <th style="width: 120px;">Durée</th>
                                        </tr>
                                    </thead>
                                    <tbody id="prestations-table-body">
                                        <!-- Les prestations seront affichées ici via JavaScript après recherche -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Résumé</label>
                        <div class="col-sm-10">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Prix Total (FCFA)</label>
                                                <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="0" readonly />
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Prix Promotionnel (FCFA)</label>
                                                <input type="number" step="0.01" class="form-control" id="prix_promo" name="prix_promo" value="{{ old('prix_promo') }}" placeholder="Prix promotionnel (optionnel)" />
                                                <small class="form-text text-info"><i class="bx bx-info-circle"></i> Si renseigné, ce prix remplacera le prix standard.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Durée Totale</label>
                                                <input type="time" class="form-control" id="duree" name="duree" value="00:00" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-success mb-0" id="resume-prestations">
                                        <p class="mb-0">Aucune prestation sélectionnée</p>
                                    </div>
                                    
                                    <!-- Section Fidélité -->
                                    <div class="mt-3 pt-3 border-top" id="fidelite-section" style="display:none;">
                                        <h6><i class="bx bxs-star text-warning"></i> Points de Fidélité</h6>
                                        <div class="alert alert-warning mb-2">
                                            <p class="mb-0"><strong>Points disponibles:</strong> <span id="points-disponibles">0</span></p>
                                            <p class="mb-0 mt-1">Tous les 5 points, bénéficiez d'une séance gratuite!</p>
                                        </div>
                                        
                                        <div class="form-check form-switch" id="utiliser-points-container" style="display:none;">
                                            <input class="form-check-input" type="checkbox" id="utiliser_points" name="utiliser_points" value="1">
                                            <label class="form-check-label" for="utiliser_points">
                                                Utiliser 5 points pour une séance gratuite
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="commentaire">Commentaire</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3">{{ old('commentaire') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Rappel automatique</label>
                        <div class="col-sm-10">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="creer_rappel" name="creer_rappel" value="1" checked>
                                <label class="form-check-label" for="creer_rappel">
                                    Créer automatiquement un rappel de prochain rendez-vous dans 2 semaines
                                </label>
                            </div>
                            <div class="form-text text-info">
                                <i class="bx bx-info-circle me-1"></i>
                                Un rappel de rendez-vous sera automatiquement créé 14 jours après cette séance.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <a href="{{ route('seances.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Préchargement des données des prestations
        const prestationsData = {
            @foreach($prestations as $prestation)
                "{{ $prestation->id }}": {
                    prix: "{{ $prestation->prix }}",
                    duree: "{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}",
                    nom: "{{ $prestation->nom_prestation }}",
                    prix_formatte: "{{ number_format($prestation->prix, 0, ',', ' ') }}"
                },
            @endforeach
        };
        
        // Liste complète des prestations pour la recherche
        const toutesLesPrestation = [
            @foreach($prestations as $prestation)
                {
                    id: "{{ $prestation->id }}",
                    nom: "{{ $prestation->nom_prestation }}",
                    prix: "{{ $prestation->prix }}",
                    prix_formatte: "{{ number_format($prestation->prix, 0, ',', ' ') }}",
                    duree: "{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}"
                },
            @endforeach
        ];

        // Recherche client par numéro de téléphone
        document.getElementById('searchClient').addEventListener('click', function() {
            const phone = document.getElementById('numero_telephone').value;
            if (!phone) {
                alert('Veuillez saisir un numéro de téléphone');
                return;
            }
            
            fetch(`{{ route('seances.getClientByPhone') }}?phone=${encodeURIComponent(phone)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('nom_complet').value = data.client.nom_complet;
                        document.getElementById('adresse_mail').value = data.client.adresse_mail;
                        
                        // Masquer l'astérisque rouge car le client existe
                        document.getElementById('nom-required').style.display = 'none';
                        
                        // Afficher la section de fidélité et les points disponibles
                        document.getElementById('fidelite-section').style.display = 'block';
                        const pointsDisponibles = data.client.points || 0;
                        document.getElementById('points-disponibles').textContent = pointsDisponibles;
                        
                        // Afficher l'option d'utiliser des points si le client a au moins 5 points
                        if (pointsDisponibles >= 5) {
                            document.getElementById('utiliser-points-container').style.display = 'block';
                        } else {
                            document.getElementById('utiliser-points-container').style.display = 'none';
                        }
                        
                        alert('Client trouvé et informations remplies automatiquement');
                    } else {
                        // Afficher l'astérisque rouge car le client n'existe pas
                        document.getElementById('nom-required').style.display = 'inline';
                        
                        // Masquer la section de fidélité si aucun client n'est trouvé
                        document.getElementById('fidelite-section').style.display = 'none';
                        document.getElementById('utiliser-points-container').style.display = 'none';
                        
                        alert(data.message + '. Veuillez compléter le nom complet du client (obligatoire).');
                        document.getElementById('nom_complet').focus();
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la recherche du client');
                });
        });
        
        // Gestion des checkboxes de prestations et calcul du total
        let checkboxes = document.querySelectorAll('.prestation-checkbox');
        const prixTotalInput = document.getElementById('prix');
        const dureeTotaleInput = document.getElementById('duree');
        const resumePrestations = document.getElementById('resume-prestations');
        
        // Fonction pour calculer le prix total et la durée totale
        function calculerTotaux() {
            let prixTotal = 0;
            let heuresTotal = 0;
            let minutesTotal = 0;
            let prestationsSelectionnees = [];
            
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const prestationId = checkbox.value;
                    
                    // Récupérer les informations de la prestation
                    const prix = parseFloat(checkbox.getAttribute('data-prix'));
                    const duree = checkbox.getAttribute('data-duree');
                    const [heures, minutes] = duree.split(':').map(Number);
                    
                    // Calculer le total
                    prixTotal += prix;
                    heuresTotal += heures;
                    minutesTotal += minutes;
                    
                    // Ajouter à la liste des prestations sélectionnées
                    const nomPrestation = checkbox.closest('tr').querySelector('td:nth-child(2)').textContent;
                    prestationsSelectionnees.push(`${nomPrestation} (${duree})`);
                }
            });
            
            // Convertir les minutes en heures si nécessaire
            heuresTotal += Math.floor(minutesTotal / 60);
            minutesTotal = minutesTotal % 60;
            
            // Mettre à jour les champs
            prixTotalInput.value = prixTotal.toFixed(2);
            dureeTotaleInput.value = `${String(heuresTotal).padStart(2, '0')}:${String(minutesTotal).padStart(2, '0')}`;
            
            // Mettre à jour le résumé
            if (prestationsSelectionnees.length > 0) {
                resumePrestations.innerHTML = `
                    <p class="mb-0"><strong>${prestationsSelectionnees.length}</strong> prestation(s) sélectionnée(s) :</p>
                    <ul class="mb-0 mt-1">
                        ${prestationsSelectionnees.map(p => `<li>${p}</li>`).join('')}
                    </ul>
                `;
            } else {
                resumePrestations.innerHTML = `<p class="mb-0">Aucune prestation sélectionnée</p>`;
            }
        }
        
        // Fonction pour mettre à jour les event listeners des checkboxes après filtrage
        function actualiserEventListeners() {
            // Mettre à jour la référence globale aux checkboxes
            checkboxes = document.querySelectorAll('.prestation-checkbox');
            
            // Supprimer les anciens événements pour éviter les doublons
            checkboxes.forEach(checkbox => {
                checkbox.removeEventListener('change', calculerTotaux);
                checkbox.addEventListener('change', calculerTotaux);
            });
        }
        
        // Fonction pour retirer les accents d'une chaîne
        function removeAccents(str) {
            return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        // Recherche de prestations
        const rechercheInput = document.getElementById('recherche-prestation');
        const prestationsTableBody = document.getElementById('prestations-table-body');
        
        rechercheInput.addEventListener('input', function() {
            const valeur = this.value.toLowerCase().trim();
            const valeurSansAccent = removeAccents(valeur);
            let html = '';
            
            // Si la valeur est vide, ne rien afficher
            if (valeur === '') {
                prestationsTableBody.innerHTML = '';
                return;
            }
            
            // Filtrer les prestations selon la recherche (insensible aux accents)
            const resultats = toutesLesPrestation.filter(prestation => { 
                const nomSansAccent = removeAccents(prestation.nom.toLowerCase());
                return nomSansAccent.includes(valeurSansAccent);
            });
            
            // Générer le HTML pour les résultats
            resultats.forEach(prestation => {
                html += `
                <tr>
                    <td class="text-center">
                        <div class="form-check">
                            <input class="form-check-input prestation-checkbox" type="checkbox" 
                                value="${prestation.id}" id="prestation_${prestation.id}" 
                                name="prestations[]" data-prix="${prestation.prix}" 
                                data-duree="${prestation.duree}">
                        </div>
                    </td>
                    <td>${prestation.nom}</td>
                    <td class="text-end">${prestation.prix_formatte}</td>
                    <td>${prestation.duree}</td>
                </tr>
                `;
            });
            
            // Si aucun résultat
            if (resultats.length === 0) {
                html = `<tr><td colspan="4" class="text-center">Aucune prestation correspondante</td></tr>`;
            }
            
            prestationsTableBody.innerHTML = html;
            actualiserEventListeners();
            // Ne pas appeler calculerTotaux() ici car aucune case n'est encore cochée
        });
        
        // Initialisation
        calculerTotaux();
    });
</script>
@endsection
