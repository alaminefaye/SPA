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
                            @if (isset($tousSalonsOccupes) && $tousSalonsOccupes)
                                <div class="alert alert-warning mb-2">
                                    <i class="bx bx-error-circle me-1"></i>
                                    Tous les salons sont occupés. La séance sera <strong>automatiquement mise en file d'attente</strong> après enregistrement.
                                </div>
                            @endif
                            
                            <select class="form-select" id="salon_id" name="salon_id" required>
                                <option value="">Sélectionnez un salon</option>
                                
                                @foreach($tousLesSalons as $salon)
                                    @php
                                        $isDisponible = in_array($salon->id, $salonsDisponiblesIds);
                                        $isCurrentSalon = $seance->salon_id == $salon->id;
                                        $disabled = !$isDisponible && !$isCurrentSalon;
                                    @endphp
                                    <option value="{{ $salon->id }}" 
                                        {{ (old('salon_id') ?? $seance->salon_id) == $salon->id ? 'selected' : '' }}
                                        {{ $disabled ? 'disabled' : '' }}
                                    >
                                        {{ $salon->nom }} 
                                        @if(!$isDisponible && !$isCurrentSalon) 
                                            (Occupé) 
                                        @elseif(!$isDisponible && $isCurrentSalon) 
                                            (Salon actuel) 
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            
                            @if (isset($salonsOccupesCount) && $salonsOccupesCount > 0)
                                <div class="form-text text-warning">
                                    <i class="bx bx-info-circle"></i>
                                    {{ $salonsOccupesCount }} salon(s) actuellement occupé(s) et ne peuvent pas être sélectionnés.
                                </div>
                                <div class="alert alert-info mt-2">
                                    <i class="bx bx-bulb me-1"></i>
                                    <strong>Important :</strong> Si vous sélectionnez un salon occupé et que vous choisissez le statut "Planifiée", la séance sera <strong>automatiquement mise en file d'attente</strong> après enregistrement.
                                </div>
                            @endif
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
                                                        data-duree="{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}"
                                                        {{ in_array($prestation->id, $seance->prestations->pluck('id')->toArray()) ? 'checked' : '' }}>
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
                                                <input type="number" step="0.01" class="form-control" id="prix" name="prix" value="{{ old('prix') ?? $seance->prix }}" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Durée Totale</label>
                                                <input type="time" class="form-control" id="duree" name="duree" value="{{ old('duree') ?? $seance->duree->format('H:i') }}" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-success mb-0" id="resume-prestations">
                                        <p class="mb-0">Prestations sélectionnées</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="statut">Statut *</label>
                        <div class="col-sm-10">
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="planifiee" {{ (old('statut') ?? $seance->statut) == 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                                <option value="en_cours" {{ (old('statut') ?? $seance->statut) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminee" {{ (old('statut') ?? $seance->statut) == 'terminee' ? 'selected' : '' }}>Terminée</option>
                                <option value="annulee" {{ (old('statut') ?? $seance->statut) == 'annulee' ? 'selected' : '' }}>Annulée</option>
                                <option value="en_attente" {{ (old('statut') ?? $seance->statut) == 'en_attente' ? 'selected' : '' }}>En file d'attente</option>
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

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
