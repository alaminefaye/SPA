@extends('layouts.app')

@section('title', 'Créer une Réservation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion / <a href="{{ route('reservations.index') }}">Réservations</a> /</span> Créer
    </h4>

    <div class="row">
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Nouvelle Réservation</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible mb-3">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="numero_telephone">Téléphone Client</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="numero_telephone" name="numero_telephone" placeholder="Numéro de téléphone" value="{{ old('numero_telephone') }}" required>
                                    <button class="btn btn-outline-primary" type="button" id="searchClientBtn">Rechercher</button>
                                </div>
                                <div class="form-text">Entrez le numéro de téléphone du client et cliquez sur Rechercher</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nom_complet">Nom du Client</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nom_complet" name="nom_complet" placeholder="Nom complet" value="{{ old('nom_complet') }}">
                                <div class="form-text">Requis si le client n'existe pas encore</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="adresse_mail">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="adresse_mail" name="adresse_mail" placeholder="Email" value="{{ old('adresse_mail') }}">
                                <div class="form-text">Requis si le client n'existe pas encore</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="salon_id">Salon</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="salon_id" name="salon_id" required>
                                    <option value="">Sélectionner un salon</option>
                                    @foreach($salons as $salon)
                                    <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>{{ $salon->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Prestations</label>
                            <div class="col-sm-10">
                                <div class="alert alert-info mb-2">
                                    Sélectionnez une ou plusieurs prestations. Le prix total et la durée totale seront automatiquement calculés.
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="searchPrestation" placeholder="Rechercher une prestation...">
                                    <div class="form-text">Commencez à taper pour voir les prestations disponibles</div>
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
                                        <tbody>
                                            @foreach($prestations as $prestation)
                                            <tr>
                                                <td class="text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input prestation-checkbox" type="checkbox" 
                                                            value="{{ $prestation->id }}" id="prestation_{{ $prestation->id }}" 
                                                            name="prestations[]" data-prix="{{ $prestation->prix }}" 
                                                            data-duree="{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}"
                                                            data-nom="{{ $prestation->nom_prestation }}"
                                                            {{ (is_array(old('prestations')) && in_array($prestation->id, old('prestations'))) ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td>{{ $prestation->nom_prestation }}</td>
                                                <td class="text-end">{{ number_format($prestation->prix, 0, ',', ' ') }}</td>
                                                <td>{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}</td>
                                            </tr>
                                            @endforeach
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
                                                    <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="{{ old('prix', 0) }}" required readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <label class="form-label">Durée Totale</label>
                                                    <input type="time" class="form-control" id="duree" name="duree" value="{{ old('duree', '00:00') }}" required readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="alert alert-success mb-0" id="resume-prestations">
                                            <p class="mb-0">Aucune prestation sélectionnée</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="date_heure">Date et Heure</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" class="form-control" id="date_heure" name="date_heure" value="{{ old('date_heure') }}" required>
                            </div>
                        </div>
                        

                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="commentaire">Commentaire</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="3">{{ old('commentaire') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du champ de recherche des prestations
        const searchInput = document.getElementById('searchPrestation');
        const prestationRows = document.querySelectorAll('.prestation-checkbox').forEach(checkbox => {
            // Cacher toutes les lignes de prestation au chargement initial
            const row = checkbox.closest('tr');
            row.style.display = 'none';
        });
        
        // Fonction pour retirer les accents d'une chaîne
        function removeAccents(str) {
            return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }

        // Fonction pour filtrer les prestations basée sur la recherche
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const searchTermWithoutAccent = removeAccents(searchTerm);
            
            document.querySelectorAll('.prestation-checkbox').forEach(checkbox => {
                const row = checkbox.closest('tr');
                const prestationName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const prestationNameWithoutAccent = removeAccents(prestationName);
                
                if (searchTerm === '') {
                    // Si le champ de recherche est vide, ne rien afficher
                    row.style.display = 'none';
                } else if (prestationName.includes(searchTerm) || prestationNameWithoutAccent.includes(searchTermWithoutAccent)) {
                    // Afficher les lignes qui correspondent à la recherche (avec ou sans accent)
                    row.style.display = '';
                } else {
                    // Cacher les lignes qui ne correspondent pas à la recherche
                    row.style.display = 'none';
                }
            });
        });

        // Recherche de client par téléphone
        document.getElementById('searchClientBtn').addEventListener('click', function() {
            const phone = document.getElementById('numero_telephone').value;
            if (!phone) {
                alert('Veuillez entrer un numéro de téléphone');
                return;
            }
            
            fetch(`{{ route('reservations.getClientByPhone') }}?phone=${encodeURIComponent(phone)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('nom_complet').value = data.client.nom_complet;
                        document.getElementById('adresse_mail').value = data.client.adresse_mail;
                    } else {
                        alert(data.message);
                        document.getElementById('nom_complet').value = '';
                        document.getElementById('adresse_mail').value = '';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la recherche du client');
                });
        });
        
        // Gestion des prestations multiples avec cases à cocher
        const prestationCheckboxes = document.querySelectorAll('.prestation-checkbox');
        const prixInput = document.getElementById('prix');
        const dureeInput = document.getElementById('duree');
        const resumePrestations = document.getElementById('resume-prestations');
        
        // Fonction pour calculer le prix et la durée totale
        function updateTotals() {
            const selectedPrestations = Array.from(document.querySelectorAll('.prestation-checkbox:checked'));
            let totalPrix = 0;
            let totalHeures = 0;
            let totalMinutes = 0;
            
            if (selectedPrestations.length === 0) {
                prixInput.value = '0';
                dureeInput.value = '00:00';
                resumePrestations.innerHTML = '<p class="mb-0">Aucune prestation sélectionnée</p>';
                resumePrestations.className = 'alert alert-success mb-0';
                return;
            }
            
            // Récupérer les noms des prestations sélectionnées pour l'affichage dans le résumé
            const selectedPrestationNames = selectedPrestations.map(checkbox => {
                return checkbox.dataset.nom || checkbox.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
            });
            
            // Calculer le prix total
            totalPrix = selectedPrestations.reduce((sum, checkbox) => {
                return sum + parseFloat(checkbox.dataset.prix);
            }, 0);
            
            // Calculer la durée totale
            selectedPrestations.forEach(checkbox => {
                const dureeStr = checkbox.dataset.duree;
                const [hours, minutes] = dureeStr.split(':').map(Number);
                totalHeures += hours;
                totalMinutes += minutes;
            });
            
            // Conversion des minutes en heures si > 60
            totalHeures += Math.floor(totalMinutes / 60);
            totalMinutes = totalMinutes % 60;
            
            // Formatage de la durée au format HH:MM
            const formattedHours = String(totalHeures).padStart(2, '0');
            const formattedMinutes = String(totalMinutes).padStart(2, '0');
            const totalDuree = `${formattedHours}:${formattedMinutes}`;
            
            // Mise à jour des champs
            prixInput.value = totalPrix;
            dureeInput.value = totalDuree;
            
            // Mise à jour du résumé
            let resumeHTML = '<p class="mb-0"><strong>Prestations sélectionnées :</strong></p><ul class="mb-0">';
            selectedPrestationNames.forEach(name => {
                resumeHTML += `<li>${name}</li>`;
            });
            resumeHTML += '</ul>';
            resumePrestations.innerHTML = resumeHTML;
            resumePrestations.className = 'alert alert-primary mb-0';
        }
        
        // Ajouter les écouteurs d'événements à chaque case à cocher
        prestationCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateTotals);
        });
        
        // Initialiser les totaux au chargement de la page
        updateTotals();
    });
</script>
@endsection
