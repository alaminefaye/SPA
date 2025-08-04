@extends('layouts.app')

@section('title', 'Détails du Salon')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('salons.index') }}">Salons</a> /</span> Détails
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Détails du Salon</h5>
                <div>
                    <a href="{{ route('salons.edit', $salon->id) }}" class="btn btn-primary btn-sm me-2">
                        <i class="bx bx-edit-alt me-1"></i> Modifier
                    </a>
                    <form action="{{ route('salons.destroy', $salon->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce salon?')">
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
                        {{ $salon->id }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>Nom du Salon:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $salon->nom }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>Créé le:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $salon->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <strong>Dernière modification:</strong>
                    </div>
                    <div class="col-sm-10">
                        {{ $salon->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('salons.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
