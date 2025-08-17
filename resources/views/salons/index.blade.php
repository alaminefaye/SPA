@extends('layouts.app')

@section('title', 'Salons')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion /</span> Salons
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des Salons</h5>
        @can('create salons')
        <a href="{{ route('salons.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Ajouter un salon
        </a>
        @endcan
    </div>
    <div class="card-body">
        <!-- Formulaire de recherche -->
        <form action="{{ route('salons.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Rechercher un salon par nom..." value="{{ $search ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">Rechercher</button>
                    @if(request()->has('search'))
                        <a href="{{ route('salons.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                    @endif
                </div>
            </div>
        </form>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom du Salon</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($salons as $salon)
                        <tr>
                            <td>{{ $salon->id }}</td>
                            <td>{{ $salon->nom }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('view salons')
                                        <a class="dropdown-item" href="{{ route('salons.show', $salon->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> Voir
                                        </a>
                                        @endcan
                                        @can('edit salons')
                                        <a class="dropdown-item" href="{{ route('salons.edit', $salon->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        @endcan
                                        @can('delete salons')
                                        <form action="{{ route('salons.destroy', $salon->id) }}" method="POST" 
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce salon?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">
                                                <i class="bx bx-trash me-1"></i> Supprimer
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Aucun salon trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 px-2">
            {{ $salons->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
