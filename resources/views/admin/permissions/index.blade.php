@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestion des Permissions</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des permissions</h6>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-user-tag mr-1"></i> Gérer les rôles
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form action="{{ route('permissions.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Rechercher une permission..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">Rechercher</button>
                        @if(request()->has('search'))
                            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                        @endif
                    </div>
                </div>
            </form>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Les permissions sont définies au niveau du système et ne peuvent pas être modifiées via l'interface. Elles sont attribuées aux rôles dans la section de gestion des rôles.
            </div>

            <div class="row">
                @foreach($permissions as $group => $groupPermissions)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">{{ ucfirst($group) }}</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach($groupPermissions as $permission)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>{{ $permission->name }}</div>
                                            <a href="{{ route('permissions.show', $permission) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
