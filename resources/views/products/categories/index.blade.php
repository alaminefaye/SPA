@extends('layouts.app')

@section('title', 'Catégories de Produits')

@section('page-css')
<style>
  .btn-action {
    padding: 0.3rem 0.6rem;
    font-size: 0.85rem;
    margin-right: 0.5rem;
    border-radius: 0.25rem;
  }
  .action-icons {
    display: flex;
    justify-content: center;
  }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Catégories de Produits</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des catégories</h6>
            <a href="{{ route('product-categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajouter une catégorie
            </a>
        </div>
        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form action="{{ route('product-categories.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Rechercher une catégorie..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">Rechercher</button>
                        @if(request()->has('search'))
                            <a href="{{ route('product-categories.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                        @endif
                    </div>
                </div>
            </form>

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
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Produits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $category->products_count }}</span>
                                </td>
                                <td>
                                    <div class="action-icons d-flex justify-content-center">
                                        <a href="{{ route('product-categories.show', $category) }}" class="btn btn-icon btn-info btn-action me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir détails">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="{{ route('product-categories.edit', $category) }}" class="btn btn-icon btn-primary btn-action me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                        <form action="{{ route('product-categories.destroy', $category) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-danger btn-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucune catégorie trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
        
        // Activer les tooltips Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
