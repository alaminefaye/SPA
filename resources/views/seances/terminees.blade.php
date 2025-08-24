@extends('layouts.app')

@section('title', 'Séances Terminées')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / Séances /</span> Terminées
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des séances terminées</h5>
        <a href="{{ route('seances.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Nouvelle Séance
        </a>
    </div>
    
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <form action="{{ route('seances.terminees') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Rechercher..." name="search" value="{{ $search }}">
                <button class="btn btn-outline-primary" type="submit">Rechercher</button>
                @if($search ?? false)
                    <a href="{{ route('seances.terminees') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                @endif
            </div>
        </form>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Salon</th>
                        <th>Prestation</th>
                        <th>Statut</th>
                        <th>Fin (prévue)</th>
                        <th>Fin (réelle)</th>
                        <th>Respect/Retard</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($seances as $seance)
                        <tr>
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
                                <span class="badge bg-success">Terminée</span>
                            </td>
                            <td>
                                @php
                                // Calcul de l'heure de fin prévue
                                $heureFin = null;
                                if ($seance->heure_debut) {
                                    // Convertir la durée totale en minutes
                                    $dureeMinutes = 0;
                                    foreach ($seance->prestations as $prestation) {
                                        $dureeStr = is_object($prestation->duree) ? $prestation->duree->format('H:i:s') : $prestation->duree;
                                        $dureeParts = explode(':', $dureeStr);
                                        $dureeMinutes += $dureeParts[0] * 60 + $dureeParts[1];
                                    }
                                    
                                    // Calculer l'heure de fin prévue
                                    $heureDebut = is_object($seance->heure_debut) ? $seance->heure_debut : new DateTime($seance->heure_debut);
                                    $heureFin = clone $heureDebut;
                                    $heureFin->add(new DateInterval('PT'.$dureeMinutes.'M'));
                                }
                                @endphp
                                
                                {{ $heureFin ? $heureFin->format('H:i') : 'N/A' }}
                            </td>
                            <td>
                                {{ $seance->heure_fin ? (is_object($seance->heure_fin) ? $seance->heure_fin->format('H:i') : date('H:i', strtotime($seance->heure_fin))) : 'N/A' }}
                            </td>
                            <td>
                                @php
                                $respect = 'N/A';
                                $badgeClass = 'bg-secondary';
                                
                                if ($seance->heure_debut && $seance->heure_fin && isset($heureFin)) {
                                    $heureFinReelle = is_object($seance->heure_fin) ? $seance->heure_fin : new DateTime($seance->heure_fin);
                                    $diff = $heureFinReelle->getTimestamp() - $heureFin->getTimestamp();
                                    
                                    if ($diff <= 0) {
                                        $respect = 'Respect';
                                        $badgeClass = 'bg-success';
                                    } else {
                                        // Convertir la différence en minutes
                                        $retardMinutes = ceil(abs($diff) / 60);
                                        $respect = 'Retard ' . $retardMinutes . ' min';
                                        $badgeClass = 'bg-danger';
                                    }
                                }
                                @endphp
                                
                                <span class="badge {{ $badgeClass }}">{{ $respect }}</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('seances.show', $seance->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> Voir
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucune séance terminée trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3 px-2">
            {{ $seances->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                html: true,
                trigger: 'hover'
            })
        });
    });
</script>
@endsection
