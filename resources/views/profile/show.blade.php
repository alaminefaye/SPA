@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Compte /</span> Mon Profil
    </h4>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Détails du profil</h5>
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <div class="avatar-wrapper">
                            <div class="avatar avatar-xl">
                                @if($user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Photo de profil" class="rounded-circle">
                                @else
                                    <span class="avatar-initial rounded-circle bg-primary">{{ substr($user->name, 0, 1) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="button-wrapper">
                            <div class="mb-3 col-12 mb-0">
                                <div class="alert alert-primary">
                                    <h6 class="alert-heading fw-bold mb-1">{{ $user->name }}</h6>
                                    <p class="mb-0">{{ implode(', ', $user->getRoleNames()->toArray()) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Nom complet</label>
                            <div class="form-control">{{ $user->name }}</div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Email</label>
                            <div class="form-control">{{ $user->email }}</div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Date d'inscription</label>
                            <div class="form-control">{{ $user->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Dernière modification</label>
                            <div class="form-control">{{ $user->updated_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary me-2">Modifier le profil</a>
                            <button class="btn btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="bx bx-lock-alt me-1"></i> Changer le mot de passe
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de changement de mot de passe -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer mon mot de passe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••" required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="••••••••" required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
