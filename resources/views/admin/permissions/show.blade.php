@extends('layouts.app')

@section('title', 'Détails de la Permission')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails de la Permission</h1>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Permission : {{ $permission->name }}</h6>
                    <div>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th style="width: 150px;">ID:</th>
                                                <td>{{ $permission->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nom:</th>
                                                <td>{{ $permission->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Guard Name:</th>
                                                <td>{{ $permission->guard_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Créée le:</th>
                                                <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Rôles avec cette permission</h5>
                    
                    @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom du rôle</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                <a href="{{ route('roles.show', $role) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucun rôle n'a cette permission.
                        </div>
                    @endif

                    <h5 class="mb-3 mt-4">Utilisateurs avec cette permission</h5>
                    
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Via Rôle</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @foreach($user->roles as $userRole)
                                                    <span class="badge bg-primary">{{ $userRole->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @can('view users')
                                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucun utilisateur n'a cette permission.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
