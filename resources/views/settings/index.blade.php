@extends('layouts.app')

@section('title', 'Paramètres')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Compte /</span> Paramètres
    </h4>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Paramètres de l'application</h5>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <h5 class="mb-3">Notifications</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="notification_email" name="notification_email" 
                                        {{ session('user_settings.notification_email', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notification_email">Recevoir des notifications par email</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notification_app" name="notification_app" 
                                        {{ session('user_settings.notification_app', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notification_app">Recevoir des notifications dans l'application</label>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3">Apparence</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="theme" class="form-label">Thème</label>
                                    <select id="theme" name="theme" class="form-select">
                                        <option value="light" {{ session('user_settings.theme', 'light') === 'light' ? 'selected' : '' }}>Clair</option>
                                        <option value="dark" {{ session('user_settings.theme', 'light') === 'dark' ? 'selected' : '' }}>Sombre</option>
                                        <option value="auto" {{ session('user_settings.theme', 'light') === 'auto' ? 'selected' : '' }}>Auto (selon système)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3">Langue</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="language" class="form-label">Langue de l'interface</label>
                                    <select id="language" name="language" class="form-select">
                                        <option value="fr" {{ session('user_settings.language', 'fr') === 'fr' ? 'selected' : '' }}>Français</option>
                                        <option value="en" {{ session('user_settings.language', 'fr') === 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Enregistrer</button>
                            <button type="reset" class="btn btn-outline-secondary">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
