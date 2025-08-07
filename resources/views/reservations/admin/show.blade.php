@extends('layouts.app')

@section('title', 'Détails de la Réservation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion / <a href="{{ route('reservations.index') }}">Réservations</a> /</span> Détails
    </h4>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Détails de la Réservation #{{ $reservation->id }}</h5>
            <div>
                <a href="{{ route('reservations.edit', $reservation->id) }}" class="btn btn-primary btn-sm me-2">
                    <i class="bx bx-edit-alt me-1"></i> Modifier
                </a>
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Retour
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Informations du Client</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Nom</td>
                                    <td>{{ $reservation->client->nom_complet }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Téléphone</td>
                                    <td>{{ $reservation->client->numero_telephone }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Email</td>
                                    <td>{{ $reservation->client->adresse_mail }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Statut de la Réservation</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Statut</td>
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
                                </tr>
                                <tr>
                                    <td class="fw-medium">Créée le</td>
                                    <td>{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Source</td>
                                    <td>{{ $reservation->client_created ? 'Créée par le client' : 'Créée par l\'administrateur' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Informations sur le Salon</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Salon</td>
                                    <td>{{ $reservation->salon->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Adresse</td>
                                    <td>{{ $reservation->salon->adresse ?? 'Non spécifiée' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Détails de la Prestation</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Prestation</td>
                                    <td>{{ $reservation->prestation->nom_prestation }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Prix</td>
                                    <td>{{ number_format($reservation->prix, 2, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Durée</td>
                                    <td>{{ $reservation->duree->format('H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Date et Heure du Rendez-vous</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Date</td>
                                    <td>{{ $reservation->date_heure->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Heure</td>
                                    <td>{{ $reservation->date_heure->format('H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Commentaires</h6>
                    <p>{{ $reservation->commentaire ?? 'Aucun commentaire' }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" 
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');" 
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bx bx-trash me-1"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
