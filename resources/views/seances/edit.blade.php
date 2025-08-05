@extends('layouts.app')

@section('title', 'Modifier une Séance')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('seances.index') }}">Séances</a> /</span> Modifier
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Modifier la Séance</h5>
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
                
                <form action="{{ route('seances.update', $seance->id) }}" method="POST" id="seanceForm">
                    @csrf
                    @method('PUT')
                    
                    <h6 class="mb-3">Informations Client</h6>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Client</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $seance->client->nom_complet }} ({{ $seance->client->numero_telephone }})" readonly />
                            <div class="form-text">Pour changer de client, créez une nouvelle séance</div>
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
                                    <option value="{{ $salon->id }}" {{ (old('salon_id') ?? $seance->salon_id) == $salon->id ? 'selected' : '' }}>
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
                                    <option value="{{ $prestation->id }}" {{ (old('prestation_id') ?? $seance->prestation_id) == $prestation->id ? 'selected' : '' }}>
                                        {{ $prestation->nom_prestation }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="prix">Prix (€) *</label>
                        <div class="col-sm-10">
                            <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="{{ old('prix') ?? $seance->prix }}" placeholder="Prix" required readonly />
                            <div class="form-text">Le prix est automatiquement rempli en fonction de la prestation sélectionnée</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="duree">Durée *</label>
                        <div class="col-sm-10">
                            <input type="time" class="form-control" id="duree" name="duree" value="{{ old('duree') ?? $seance->duree->format('H:i:s') }}" placeholder="Durée" required readonly />
                            <div class="form-text">La durée est automatiquement remplie en fonction de la prestation sélectionnée</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="statut">Statut *</label>
                        <div class="col-sm-10">
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="planifie" {{ (old('statut') ?? $seance->statut) == 'planifie' ? 'selected' : '' }}>Planifiée</option>
                                <option value="en_cours" {{ (old('statut') ?? $seance->statut) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ (old('statut') ?? $seance->statut) == 'termine' ? 'selected' : '' }}>Terminée</option>
                                <option value="annule" {{ (old('statut') ?? $seance->statut) == 'annule' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="commentaire">Commentaire</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3">{{ old('commentaire') ?? $seance->commentaire }}</textarea>
                        </div>
                    </div>
                    
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            <a href="{{ route('seances.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-remplissage des détails de prestation
        document.getElementById('prestation_id').addEventListener('change', function() {
            const prestationId = this.value;
            if (!prestationId) {
                document.getElementById('prix').value = '';
                document.getElementById('duree').value = '';
                return;
            }
            
            fetch(`{{ route('seances.getPrestationDetails') }}?prestation_id=${encodeURIComponent(prestationId)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('prix').value = data.prestation.prix;
                        
                        // Format time for input
                        const dureeParts = data.prestation.duree.split(':');
                        const formattedDuree = `${dureeParts[0]}:${dureeParts[1]}`;
                        document.getElementById('duree').value = formattedDuree;
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
