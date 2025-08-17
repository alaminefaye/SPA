@extends('layouts.app')

@section('title', 'Gestion des points de fidélité')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion clients /</span> Points de fidélité
    </h4>

    <div class="row">
        <!-- Statistiques des points -->
        <div class="col-md-12 mb-4">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="card-info">
                                    <p class="card-text">Total des points</p>
                                    <div class="d-flex align-items-end mb-2">
                                        <h4 class="card-title mb-0">{{ number_format($statsPoints->total_points) }}</h4>
                                    </div>
                                </div>
                                <div class="card-icon">
                                    <span class="badge bg-label-primary rounded p-2">
                                        <i class="bx bx-award bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="card-info">
                                    <p class="card-text">Moyenne par client</p>
                                    <div class="d-flex align-items-end mb-2">
                                        <h4 class="card-title mb-0">{{ number_format($statsPoints->moyenne_points, 1) }}</h4>
                                    </div>
                                </div>
                                <div class="card-icon">
                                    <span class="badge bg-label-info rounded p-2">
                                        <i class="bx bx-line-chart bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="card-info">
                                    <p class="card-text">Maximum points</p>
                                    <div class="d-flex align-items-end mb-2">
                                        <h4 class="card-title mb-0">{{ $statsPoints->max_points }}</h4>
                                    </div>
                                </div>
                                <div class="card-icon">
                                    <span class="badge bg-label-success rounded p-2">
                                        <i class="bx bx-crown bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="card-info">
                                    <p class="card-text">Clients éligibles</p>
                                    <div class="d-flex align-items-end mb-2">
                                        <h4 class="card-title mb-0">{{ $statsPoints->eligible_seance_gratuite }}</h4>
                                    </div>
                                    <small>Séances gratuites</small>
                                </div>
                                <div class="card-icon">
                                    <span class="badge bg-label-warning rounded p-2">
                                        <i class="bx bx-gift bx-sm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des clients avec leurs points</h5>
        </div>
        
        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form action="{{ route('loyalty-points.index') }}" method="GET" class="mb-3">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Rechercher un client..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">Rechercher</button>
                        @if(request()->has('search'))
                            <a href="{{ route('loyalty-points.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                        @endif
                    </div>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Contact</th>
                            <th>Points</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>{{ $client->nom_complet }}</td>
                            <td>
                                {{ $client->numero_telephone }}<br>
                                <small>{{ $client->adresse_mail }}</small>
                            </td>
                            <td><span class="badge bg-primary">{{ $client->points }}</span></td>
                            <td>
                                @if($client->peutObtenirSeanceGratuite())
                                    <span class="badge bg-success">Éligible pour séance gratuite</span>
                                @else
                                    <span class="badge bg-secondary">{{ 5 - $client->points }} point(s) manquant(s)</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('manage loyalty points')
                                        <a class="dropdown-item" href="{{ route('loyalty-points.edit', $client) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier les points
                                        </a>
                                        @endcan
                                        @can('view loyalty points')
                                        <a class="dropdown-item" href="{{ route('loyalty-points.history', $client) }}">
                                            <i class="bx bx-history me-1"></i> Historique des points
                                        </a>
                                        @endcan
                                        <a class="dropdown-item" href="{{ route('clients.show', $client) }}">
                                            <i class="bx bx-user me-1"></i> Voir le profil
                                        </a>
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
            
            <div class="mt-3">
                {{ $clients->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
