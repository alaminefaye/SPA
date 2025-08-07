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
                            <label class="col-sm-2 col-form-label" for="prestation_id">Prestation</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="prestation_id" name="prestation_id" required>
                                    <option value="">Sélectionner une prestation</option>
                                    @foreach($prestations as $prestation)
                                    <option value="{{ $prestation->id }}" {{ old('prestation_id') == $prestation->id ? 'selected' : '' }}>
                                        {{ $prestation->nom_prestation }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="date_heure">Date et Heure</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" class="form-control" id="date_heure" name="date_heure" value="{{ old('date_heure') }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="prix">Prix (FCFA)</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="prix" name="prix" step="0.01" min="0" value="{{ old('prix') }}" required readonly>
                                <div class="form-text">Auto-rempli en fonction de la prestation</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="duree">Durée</label>
                            <div class="col-sm-10">
                                <input type="time" class="form-control" id="duree" name="duree" value="{{ old('duree') }}" required readonly>
                                <div class="form-text">Auto-rempli en fonction de la prestation</div>
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
        
        // Auto-remplissage des détails de prestation
        document.getElementById('prestation_id').addEventListener('change', function() {
            const prestationId = this.value;
            if (!prestationId) {
                document.getElementById('prix').value = '';
                document.getElementById('duree').value = '';
                return;
            }
            
            fetch(`{{ route('reservations.getPrestationDetails') }}?prestation_id=${prestationId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Response received:', data);
                    if (data.success) {
                        console.log('Prestation details received:', data.prestation);
                        console.log('Prix value:', data.prestation.prix);
                        console.log('Durée value:', data.prestation.duree);
                        console.log('Prix field exists:', !!document.getElementById('prix'));
                        console.log('Durée field exists:', !!document.getElementById('duree'));
                        // Remplissage du prix sans problème
                        document.getElementById('prix').value = data.prestation.prix;
                        
                        // Pour le champ durée, s'assurer qu'il est bien au format HH:MM attendu par input[type="time"]
                        const dureeValue = data.prestation.duree;
                        console.log('Trying to set duree to:', dureeValue);
                        document.getElementById('duree').value = dureeValue;
                    } else {
                        alert(data.message);
                        document.getElementById('prix').value = '';
                        document.getElementById('duree').value = '';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la récupération des détails de la prestation');
                });
        });
    });
</script>
@endsection
