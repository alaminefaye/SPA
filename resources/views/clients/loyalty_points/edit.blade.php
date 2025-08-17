@extends('layouts.app')

@section('title', 'Modifier les points de fidélité')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Points de fidélité /</span> Modifier
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modification des points de fidélité</h5>
                    <a href="{{ route('loyalty-points.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Retour à la liste
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="client-info p-3 border rounded">
                                <h6>Informations du client</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nom :</strong> {{ $client->nom_complet }}</p>
                                        <p><strong>Téléphone :</strong> {{ $client->numero_telephone }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Email :</strong> {{ $client->adresse_mail ?: 'Non renseigné' }}</p>
                                        <p><strong>Points actuels :</strong> <span class="badge bg-primary">{{ $client->points }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('loyalty-points.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="action" class="form-label">Action</label>
                                    <select id="action" name="action" class="form-select" required>
                                        <option value="add">Ajouter des points</option>
                                        <option value="remove">Retirer des points</option>
                                        <option value="set">Définir un nouveau total</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="points" class="form-label">Nombre de points</label>
                                    <input type="number" id="points" name="points" class="form-control" min="1" value="1" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="raison" class="form-label">Raison (optionnel)</label>
                                    <textarea id="raison" name="raison" class="form-control" rows="3" placeholder="Motif de modification des points..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save"></i> Enregistrer la modification
                                </button>
                                <a href="{{ route('loyalty-points.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
