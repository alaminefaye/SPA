@extends('layouts.app')

@section('title', 'Modification d\'utilisateur')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Administration / Utilisateurs /</span> Modifier un utilisateur
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'utilisateur: {{ $user->name }}</h5>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus placeholder="Entrez le nom complet">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Entrez l'adresse e-mail">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour conserver l'actuel)</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Entrez le nouveau mot de passe">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Laissez ce champ vide si vous ne souhaitez pas modifier le mot de passe.</small>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="text" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmez le nouveau mot de passe">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Enregistrer les modifications
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
                    <div class="user-info">
                        <p><strong>ID:</strong> {{ $user->id }}</p>
                        <p><strong>Créé le:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Dernière modification:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="alert alert-warning mb-0">
                        <h6 class="alert-heading fw-bold mb-1"><i class="bx bx-info-circle me-1"></i> Note:</h6>
                        <p class="mb-0">Si vous modifiez l'email, assurez-vous qu'il est unique et correct.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
