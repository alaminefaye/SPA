@extends('layouts.app')

@section('title', 'Détails du Rôle')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails du Rôle</h1>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Rôle : {{ $role->name }}</h6>
                    <div>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        @if($role->name !== 'super-admin')
                            @can('edit roles')
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            @endcan
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">Permissions attribuées</h5>

                    <div class="row">
                        @forelse($role->permissions->groupBy(function ($item) {
                            $parts = explode(' ', $item->name);
                            return $parts[1] ?? 'other';
                        }) as $group => $permissions)
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold">{{ ucfirst($group) }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            @foreach($permissions as $permission)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $permission->name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Aucune permission attribuée à ce rôle.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <h5 class="mb-3 mt-4">Utilisateurs avec ce rôle</h5>
                    
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Date d'attribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->pivot && $user->pivot->created_at ? $user->pivot->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
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
                            Aucun utilisateur n'a ce rôle.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
