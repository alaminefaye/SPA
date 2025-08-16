@extends('layouts.app')

@section('title', 'Gestion des Rôles')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestion des Rôles</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des rôles</h6>
            <div>
                @can('view roles')
                <a href="{{ route('permissions.index') }}" class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-key mr-1"></i> Voir les permissions
                </a>
                @endcan
                @can('create roles')
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Ajouter un rôle
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Nombre de permissions</th>
                            <th>Nombre d'utilisateurs</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->permissions_count }}</td>
                                <td>{{ $role->users_count }}</td>
                                <td>
                                    <div class="action-icons d-flex justify-content-center">
                                        @can('view roles')
                                        <a href="{{ route('roles.show', $role) }}" class="btn btn-icon btn-info btn-action me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir détails">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        @endcan
                                        @if($role->name !== 'super-admin')
                                            @can('edit roles')
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-icon btn-primary btn-action me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                            @endcan
                                            @if($role->name !== 'admin' && $role->users_count === 0)
                                                @can('delete roles')
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-danger btn-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Aucun rôle trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    $(document).ready(function() {
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
