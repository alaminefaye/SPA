@extends('layouts.app')

@section('title', 'Gestion des Produits')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestion des Produits</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des produits</h6>
            <div>
                <a href="{{ route('product-categories.index') }}" class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-tags mr-1"></i> Gérer les catégories
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Ajouter un produit
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form action="{{ route('products.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Rechercher un produit..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">Rechercher</button>
                        @if(request()->has('search'))
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
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
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="{{ $product->isLowStock() ? 'table-danger' : '' }}">
                                <td class="text-center" style="width: 100px">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-height: 60px">
                                    @else
                                        <span class="text-muted"><i class="fas fa-image fa-2x"></i></span>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ number_format($product->price, 2) }} FCFA</td>
                                <td>
                                    @if($product->stock == 0)
                                        <span class="font-weight-bold text-danger">
                                            Rupture de stock
                                        </span>
                                        <i class="fas fa-exclamation-triangle text-danger ml-1" title="Rupture de stock"></i>
                                    @else
                                        <span class="font-weight-bold {{ $product->isLowStock() ? 'text-danger' : '' }}">
                                            {{ $product->stock }}
                                        </span>
                                        @if($product->isLowStock())
                                            <i class="fas fa-exclamation-triangle text-danger ml-1" title="Stock bas"></i>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="action-icons d-flex justify-content-center">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-icon btn-info btn-action me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir détails">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-icon btn-primary btn-action me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-danger btn-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun produit trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    $(document).ready(function() {
        // Initialiser DataTable
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
        
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
