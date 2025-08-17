@extends('layouts.app')

@section('title', 'Modifier mon profil')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Compte / <a href="{{ route('profile.show') }}">Mon Profil</a> /</span> Modifier
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Modifier mon profil</h5>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
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
                                <label for="profile_photo" class="btn btn-primary me-2 mb-2">
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Télécharger une nouvelle photo</span>
                                    <input type="file" id="profile_photo" name="profile_photo" class="account-file-input" hidden accept="image/png, image/jpeg" />
                                </label>
                                <p class="text-muted mb-0">Formats autorisés: JPG, PNG. Taille maximale: 1MB.</p>
                                @error('profile_photo')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Nom complet</label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Enregistrer</button>
                                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">Modifier mon mot de passe</h5>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="current_password" class="form-label">Mot de passe actuel</label>
                                <input class="form-control @error('current_password') is-invalid @enderror" type="password" id="current_password" name="current_password" />
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6"></div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" />
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Changer le mot de passe</button>
                                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prévisualisation de la photo de profil
        const input = document.querySelector('#profile_photo');
        const avatarImage = document.querySelector('.avatar img');
        const avatarInitial = document.querySelector('.avatar-initial');
        
        if (input) {
            input.addEventListener('change', function(e) {
                if (e.target.files.length) {
                    const file = e.target.files[0];
                    const reader = new FileReader();
                    
                    reader.onload = function() {
                        // Créer une image si elle n'existe pas
                        if (!avatarImage) {
                            const newImg = document.createElement('img');
                            newImg.classList.add('rounded-circle');
                            newImg.alt = 'Photo de profil';
                            
                            // Remplacer l'initiale par l'image
                            if (avatarInitial) {
                                avatarInitial.parentNode.replaceChild(newImg, avatarInitial);
                            } else {
                                document.querySelector('.avatar').appendChild(newImg);
                            }
                            
                            newImg.src = reader.result;
                        } else {
                            avatarImage.src = reader.result;
                        }
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Toggle de visibilité des mots de passe
        const toggleBtns = document.querySelectorAll('.input-group-text');
        toggleBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const input = this.parentNode.querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bx-hide', 'bx-show');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bx-show', 'bx-hide');
                }
            });
        });
    });
</script>
@endsection
