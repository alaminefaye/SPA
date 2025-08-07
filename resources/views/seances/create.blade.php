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
                        <label class="col-sm-2 col-form-label" for="nom_complet">Nom Complet</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nom_complet" name="nom_complet" value="{{ old('nom_complet') }}" placeholder="Nom complet du client" />
                            <div class="form-text">Laissez vide si vous recherchez un client existant par téléphone</div>
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
                            <select class="form-select" id="salon_id" name="salon_id" required>
                                <option value="">Sélectionnez un salon</option>
                                @foreach($salons as $salon)
                                    <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>
                                        {{ $salon->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Prestations *</label>
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
                                                        data-duree="{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}">
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
                                                <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="0" readonly />
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
                    duree: "{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}"
                },
            @endforeach
        };

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
                        alert('Client trouvé et informations remplies automatiquement');
                    } else {
                        alert(data.message + '. Veuillez compléter les informations du client.');
                        document.getElementById('nom_complet').focus();
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la recherche du client');
                });
        });
        
        // Gestion des checkboxes de prestations et calcul du total
        const checkboxes = document.querySelectorAll('.prestation-checkbox');
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
        
        // Event listeners pour les cases à cocher
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                calculerTotaux();
            });
        });
        
        // Initialisation
        calculerTotaux();
    });
</script>
@endsection
