@extends('layouts.app')

@section('title', 'Activités de Connexion')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Activités de Connexion</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Historique des connexions</h6>
            <div>
                @can('delete login activities')
                <form action="{{ route('login-activities.clear-all') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer tous les journaux de connexion ?');">
                        <i class="bx bx-trash-alt"></i> Supprimer tout
                    </button>
                </form>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form action="{{ route('login-activities.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                            </div>
                            <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="user_id" class="form-control">
                            <option value="">Tous les utilisateurs</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="status" class="form-control">
                            <option value="">Tous les statuts</option>
                            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Réussie</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échouée</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-primary mr-2">Filtrer</button>
                        <a href="{{ route('login-activities.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Adresse IP</th>
                            <th>Appareil / Navigateur</th>
                            <th>Connexion</th>
                            <th>Déconnexion</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td>
                                    @if ($activity->user)
                                        {{ $activity->user->name }}<br>
                                        <small class="text-muted">{{ $activity->user->email }}</small>
                                    @else
                                        <span class="text-danger">Utilisateur supprimé</span>
                                    @endif
                                </td>
                                <td>{{ $activity->ip_address }}</td>
                                <td>
                                    <div><strong>Appareil:</strong> {{ $activity->device ?: 'Non détecté' }}</div>
                                    <div><strong>Navigateur:</strong> {{ $activity->browser ?: 'Non détecté' }}</div>
                                    <div><strong>Plateforme:</strong> {{ $activity->platform ?: 'Non détecté' }}</div>
                                </td>
                                <td>{{ $activity->login_at ? $activity->login_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                <td>{{ $activity->logout_at ? $activity->logout_at->format('d/m/Y H:i:s') : 'Session active ou expirée' }}</td>
                                <td>
                                    @if($activity->successful)
                                        <span class="badge badge-success">Réussie</span>
                                    @else
                                        <span class="badge badge-danger">Échec</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('login-activities.show', $activity) }}" class="btn btn-sm btn-info">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        @can('delete login activities')
                                        <form action="{{ route('login-activities.destroy', $activity) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?');">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune activité de connexion trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
