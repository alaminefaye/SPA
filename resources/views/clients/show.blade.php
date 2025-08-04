@extends('layouts.app')

@section('title', 'Détails du Client')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('clients.index') }}">Clients</a> /</span> Détails
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Détails du Client</h5>
                <div>
                    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary btn-sm me-2">
                        <i class="bx bx-edit-alt me-1"></i> Modifier
                    </a>
                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bx bx-trash me-1"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>ID:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $client->id }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>Nom complet:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $client->nom_complet }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>Numéro téléphone:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $client->numero_telephone }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>Adresse mail:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $client->adresse_mail }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>Créé le:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $client->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>Dernière modification:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $client->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
