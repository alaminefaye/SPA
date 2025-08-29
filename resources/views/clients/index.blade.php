@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion /</span> Clients
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des Clients</h5>
        <div>
            <a href="{{ route('clients.import-form') }}" class="btn btn-success me-2">
                <i class="bx bx-import me-1"></i> Importer
            </a>
            @can('create clients')
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Ajouter un client
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <!-- Formulaire de recherche -->
        <form action="{{ route('clients.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Rechercher un client..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">Rechercher</button>
                    @if(request()->has('search'))
                        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                    @endif
                </div>
            </div>
        </form>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom complet</th>
                        <th>Numéro téléphone</th>
                        <th>Adresse mail</th>
                        <th>Points</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>{{ $client->nom_complet }}</td>
                            <td>{{ $client->numero_telephone }}</td>
                            <td>{{ $client->adresse_mail }}</td>
                            <td>
                                @if($client->points >= 5)
                                    <span class="badge bg-success">{{ $client->points }} points</span>
                                    <i class="bx bxs-gift text-warning" data-bs-toggle="tooltip" title="Client éligible pour une séance gratuite"></i>
                                @elseif($client->points > 0)
                                    <span class="badge bg-info">{{ $client->points }} points</span>
                                @else
                                    <span class="badge bg-secondary">0 point</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('view clients')
                                        <a class="dropdown-item" href="{{ route('clients.show', $client->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> Voir
                                        </a>
                                        @endcan
                                        @can('edit clients')
                                        <a class="dropdown-item" href="{{ route('clients.edit', $client->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        @endcan
                                        @can('delete clients')
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" 
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-trash me-1"></i> Supprimer
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun client trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 px-2">
            {{ $clients->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips pour les badges de fidélité
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
@endsection
