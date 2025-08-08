@extends('layouts.app')

@section('title', 'Gestion des Réservations')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><span class="text-muted fw-light">Gestion /</span> Réservations</h4>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Ajouter une réservation
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Réservations</h5>
            <form action="{{ route('reservations.index') }}" method="GET" class="mb-0">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Rechercher..." name="search" value="{{ $search ?? '' }}">
                    <button class="btn btn-outline-primary" type="submit">Rechercher</button>
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Salon</th>
                        <th>Prestation</th>
                        <th>Date et Heure</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->client->nom_complet }}</td>
                            <td>{{ $reservation->salon->nom }}</td>
                            <td>
                                @if($reservation->prestations->count() > 0)
                                    @foreach($reservation->prestations as $prestation)
                                        <span class="badge bg-label-info mb-1">{{ $prestation->nom_prestation }}</span>@if(!$loop->last),<br>@endif
                                    @endforeach
                                @else
                                    <span class="badge bg-label-danger">Aucune prestation</span>
                                @endif
                            </td>
                            <td>{{ $reservation->date_heure->format('d/m/Y H:i') }}</td>
                            <td>
                                @switch($reservation->statut)
                                    @case('en_attente')
                                        <span class="badge bg-label-warning">En attente</span>
                                        @break
                                    @case('confirme')
                                        <span class="badge bg-label-info">Confirmé</span>
                                        @break
                                    @case('en_cours')
                                        <span class="badge bg-label-primary">En cours</span>
                                        @break
                                    @case('termine')
                                        <span class="badge bg-label-success">Terminé</span>
                                        @break
                                    @case('annule')
                                        <span class="badge bg-label-danger">Annulé</span>
                                        @break
                                    @default
                                        <span class="badge bg-label-secondary">{{ $reservation->statut }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('reservations.show', $reservation->id) }}">
                                            <i class="bx bx-show me-1"></i> Voir
                                        </a>
                                        <a class="dropdown-item" href="{{ route('reservations.edit', $reservation->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation?');">
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
                            <td colspan="6" class="text-center">Aucune réservation trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 px-2">
        {{ $reservations->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
