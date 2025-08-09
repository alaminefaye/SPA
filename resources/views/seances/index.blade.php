@extends('layouts.app')

@section('title', 'Gestion des Séances')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion /</span> Séances
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des Séances</h5>
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
        
        <form action="{{ route('seances.index') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Rechercher..." name="search" value="{{ $search ?? '' }}">
                <button class="btn btn-outline-primary" type="submit">Rechercher</button>
                @if($search ?? false)
                    <a href="{{ route('seances.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
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
                        <th>Prix</th>
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
                                @switch($seance->statut)
                                    @case('planifiee')
                                        <span class="badge bg-primary">Planifiée</span>
                                        @break
                                    @case('en_cours')
                                        <span class="badge bg-warning">En cours</span>
                                        @break
                                    @case('termine')
                                        <span class="badge bg-success">Terminée</span>
                                        @break
                                    @case('annule')
                                        <span class="badge bg-danger">Annulée</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($seance->is_free)
                                    <span class="badge bg-success">GRATUIT</span>
                                    <i class="bx bxs-star text-warning ms-1" data-bs-toggle="tooltip" title="Séance offerte (fidélité)"></i>
                                @else
                                    {{ number_format($seance->prix, 0, ',', ' ') }} FCFA
                                @endif
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
                                        <a class="dropdown-item" href="{{ route('seances.edit', $seance->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <form action="{{ route('seances.destroy', $seance->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette séance?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-trash me-1"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucune séance trouvée</td>
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
        // Initialisation des popovers pour les informations sur les prestations
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        
        // Initialisation des tooltips pour les badges de fidélité
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
@endsection
