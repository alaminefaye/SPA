@extends('layouts.app')

@section('title', 'Détails de l\'activité de connexion')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails de l'activité de connexion</h1>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Activité de connexion #{{ $activity->id }}
                    </h6>
                    <div>
                        <a href="{{ route('login-activities.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations de l'utilisateur</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 150px;">ID:</th>
                                                <td>{{ $activity->user->id ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nom:</th>
                                                <td>{{ $activity->user->name ?? 'Utilisateur supprimé' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email:</th>
                                                <td>{{ $activity->user->email ?? 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Détails de la connexion</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 150px;">Statut:</th>
                                                <td>
                                                    @if($activity->successful)
                                                        <span class="badge badge-success">Réussie</span>
                                                    @else
                                                        <span class="badge badge-danger">Échec</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date de connexion:</th>
                                                <td>{{ $activity->login_at ? $activity->login_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date de déconnexion:</th>
                                                <td>{{ $activity->logout_at ? $activity->logout_at->format('d/m/Y H:i:s') : 'Session active ou expirée' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations techniques</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 150px;">Adresse IP:</th>
                                                        <td>{{ $activity->ip_address ?: 'Non disponible' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Appareil:</th>
                                                        <td>{{ $activity->device ?: 'Non détecté' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Navigateur:</th>
                                                        <td>{{ $activity->browser ?: 'Non détecté' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th style="width: 150px;">Plateforme:</th>
                                                        <td>{{ $activity->platform ?: 'Non détecté' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>User Agent:</th>
                                                        <td>
                                                            <small class="text-muted">{{ $activity->user_agent ?: 'Non disponible' }}</small>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Créé le:</th>
                                                        <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('login-activities.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                        @can('delete login activities')
                        <form action="{{ route('login-activities.destroy', $activity) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
