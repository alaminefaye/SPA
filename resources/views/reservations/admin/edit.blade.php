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
                            <label class="col-sm-2 col-form-label" for="prestation_id">Prestation</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="prestation_id" name="prestation_id" required>
                                    <option value="">Sélectionner une prestation</option>
                                    @foreach($prestations as $prestation)
                                    <option value="{{ $prestation->id }}" {{ $reservation->prestation_id == $prestation->id ? 'selected' : '' }}>
                                        {{ $prestation->nom_prestation }}
                                    </option>
                                    @endforeach
                                </select>
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
                            <label class="col-sm-2 col-form-label" for="prix">Prix (FCFA)</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="prix" name="prix" step="0.01" min="0" 
                                       value="{{ $reservation->prix }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="duree">Durée</label>
                            <div class="col-sm-10">
                                <input type="time" class="form-control" id="duree" name="duree" 
                                       value="{{ $reservation->duree->format('H:i:s') }}" required>
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
        // Auto-remplissage des détails de prestation lors du changement
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
                    if (data.success) {
                        console.log('Prestation details received:', data.prestation);
                        // Remplissage du prix sans problème
                        document.getElementById('prix').value = data.prestation.prix;
                        
                        // Pour le champ durée, s'assurer qu'il est bien au format HH:MM attendu par input[type="time"]
                        const dureeValue = data.prestation.duree;
                        console.log('Trying to set duree to:', dureeValue);
                        document.getElementById('duree').value = dureeValue;
                    } else {
                        alert(data.message);
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
