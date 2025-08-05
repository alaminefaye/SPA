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
                        <label class="col-sm-2 col-form-label" for="prestation_id">Prestation *</label>
                        <div class="col-sm-10">
                            <select class="form-select" id="prestation_id" name="prestation_id" required>
                                <option value="">Sélectionnez une prestation</option>
                                @foreach($prestations as $prestation)
                                    <option value="{{ $prestation->id }}" {{ old('prestation_id') == $prestation->id ? 'selected' : '' }}>
                                        {{ $prestation->nom_prestation }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="prix">Prix (€) *</label>
                        <div class="col-sm-10">
                            <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="{{ old('prix') }}" placeholder="Prix" required readonly />
                            <div class="form-text">Le prix est automatiquement rempli en fonction de la prestation sélectionnée</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="duree">Durée *</label>
                        <div class="col-sm-10">
                            <input type="time" class="form-control" id="duree" name="duree" value="{{ old('duree') }}" placeholder="Durée" required readonly />
                            <div class="form-text">La durée est automatiquement remplie en fonction de la prestation sélectionnée</div>
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
        
        // Auto-remplissage des détails de prestation - SANS AJAX
        document.getElementById('prestation_id').addEventListener('change', function() {
            const prestationId = this.value;
            const prixInput = document.getElementById('prix');
            const dureeInput = document.getElementById('duree');
            
            if (!prestationId) {
                prixInput.value = '';
                dureeInput.value = '';
                return;
            }
            
            // Récupération directe des données pré-chargées
            if (prestationsData[prestationId]) {
                prixInput.value = prestationsData[prestationId].prix;
                dureeInput.value = prestationsData[prestationId].duree;
            } else {
                prixInput.value = '';
                dureeInput.value = '';
            }
        });
    });
</script>
@endsection
