@extends('layouts.app')

@section('title', 'Détails de la Prestation')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('prestations.index') }}">Prestations</a> /</span> Détails
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Détails de la Prestation</h5>
                <div>
                    <a href="{{ route('prestations.edit', $prestation->id) }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-edit-alt me-1"></i> Modifier
                    </a>
                    <form action="{{ route('prestations.destroy', $prestation->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette prestation?')">
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
                    <div class="col-sm-3">
                        <strong>ID:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $prestation->id }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Nom de la Prestation:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $prestation->nom_prestation }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Prix:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ number_format($prestation->prix, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Durée:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ \Carbon\Carbon::parse($prestation->duree)->format('H:i:s') }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Créé le:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $prestation->created_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Modifié le:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $prestation->updated_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-12">
                        <a href="{{ route('prestations.index') }}" class="btn btn-secondary">Retour à la liste</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
