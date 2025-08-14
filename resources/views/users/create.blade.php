@extends('layouts.app')

@section('title', 'Création d\'utilisateur')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Administration / Utilisateurs /</span> Nouvel utilisateur
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Créer un nouvel utilisateur</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Entrez le nom complet">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="Entrez l'adresse e-mail">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="passer123" required placeholder="Entrez le mot de passe">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="text" class="form-control" id="password_confirmation" name="password_confirmation" value="passer123" required placeholder="Confirmez le mot de passe">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <p>L'utilisateur créé pourra:</p>
                    <ul>
                        <li>Se connecter à l'application</li>
                        <li>Accéder au tableau de bord</li>
                        <li>Gérer les données selon les permissions</li>
                    </ul>
                    <div class="alert alert-info mb-0">
                        <h6 class="alert-heading fw-bold mb-1"><i class="bx bx-info-circle me-1"></i> Note:</h6>
                        <p class="mb-0">Assurez-vous que l'utilisateur comprend les règles d'utilisation de l'application.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
