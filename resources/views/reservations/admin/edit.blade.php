@extends('layouts.app')

@section('title', 'Modifier une Réservation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion / <a href="{{ route('reservations.index') }}">Réservations</a> /</span> Modifier
    </h4>

    <div class="row">
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Modifier la Réservation #{{ $reservation->id }}</h5>
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
                    
                    <form action="{{ route('reservations.update', $reservation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="client_info">Client</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="client_info" 
                                       value="{{ $reservation->client->nom_complet }} ({{ $reservation->client->numero_telephone }})" readonly>
                                <div class="form-text">Les informations client ne peuvent pas être modifiées ici</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="salon_id">Salon</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="salon_id" name="salon_id" required>
                                    <option value="">Sélectionner un salon</option>
                                    @foreach($salons as $salon)
                                    <option value="{{ $salon->id }}" {{ $reservation->salon_id == $salon->id ? 'selected' : '' }}>
                                        {{ $salon->nom }}
                                    </option>
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
                                                            {{ $reservation->prestations->contains($prestation->id) ? 'checked' : '' }}>
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
                                                    <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="{{ $reservation->prix }}" required readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-2">
                                                    <label class="form-label">Durée Totale</label>
                                                    <input type="time" class="form-control" id="duree" name="duree" value="{{ $reservation->duree->format('H:i') }}" required readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="alert alert-primary mb-0" id="resume-prestations">
                                            <!-- Contenu généré par JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="date_heure">Date et Heure</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" class="form-control" id="date_heure" name="date_heure" 
                                       value="{{ $reservation->date_heure->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                        

                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="statut">Statut</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="en_attente" {{ $reservation->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="confirme" {{ $reservation->statut === 'confirme' ? 'selected' : '' }}>Confirmé</option>
                                    <option value="en_cours" {{ $reservation->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="termine" {{ $reservation->statut === 'termine' ? 'selected' : '' }}>Terminé</option>
                                    <option value="annule" {{ $reservation->statut === 'annule' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="commentaire">Commentaire</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="3">{{ $reservation->commentaire }}</textarea>
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
