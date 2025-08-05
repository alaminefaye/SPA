@extends('layouts.app')

@section('title', 'Détails de la Séance')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('seances.index') }}">Séances</a> /</span> Détails
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Détails de la Séance #{{ $seance->id }}</h5>
                <div>
                    <a href="{{ route('seances.edit', $seance->id) }}" class="btn btn-primary btn-sm me-2">
                        <i class="bx bx-edit-alt me-1"></i> Modifier
                    </a>
                    <a href="{{ route('seances.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informations Client</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 35%">Nom Complet:</th>
                                    <td>{{ $seance->client->nom_complet }}</td>
                                </tr>
                                <tr>
                                    <th>Numéro Téléphone:</th>
                                    <td>{{ $seance->client->numero_telephone }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $seance->client->adresse_mail }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Informations Séance</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">

                                <tr>
                                    <th>Statut:</th>
                                    <td>
                                        @switch($seance->statut)
                                            @case('planifie')
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
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Salon</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 35%">Nom:</th>
                                    <td>{{ $seance->salon->nom }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Prestation</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 35%">Nom:</th>
                                    <td>{{ $seance->prestation->nom_prestation }}</td>
                                </tr>
                                <tr>
                                    <th>Prix:</th>
                                    <td>{{ number_format($seance->prix, 2, ',', ' ') }} €</td>
                                </tr>
                                <tr>
                                    <th>Durée:</th>
                                    <td>{{ $seance->duree->format('H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                @if($seance->commentaire)
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted">Commentaire</h6>
                        <p>{{ $seance->commentaire }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
