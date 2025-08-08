@extends('layouts.app')

@section('title', 'Détails de la Catégorie')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails de la Catégorie: {{ $productCategory->name }}</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de la catégorie</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID:</strong> {{ $productCategory->id }}
                    </div>
                    <div class="mb-3">
                        <strong>Nom:</strong> {{ $productCategory->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Description:</strong> {{ $productCategory->description ?? 'Non spécifiée' }}
                    </div>
                    <div class="mb-3">
                        <strong>Nombre de produits:</strong> {{ $productCategory->products->count() }}
                    </div>
                    <div class="mb-3">
                        <strong>Créée le:</strong> {{ $productCategory->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="mb-3">
                        <strong>Mise à jour le:</strong> {{ $productCategory->updated_at->format('d/m/Y H:i') }}
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('product-categories.edit', $productCategory) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i> Modifier
                        </a>
                        <a href="{{ route('product-categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Produits dans cette catégorie</h6>
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Ajouter un produit
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productCategory->products as $product)
                                    <tr class="{{ $product->isLowStock() ? 'table-danger' : '' }}">
                                        <td>{{ $product->name }}</td>
                                        <td>{{ number_format($product->price, 2) }} FCFA</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Aucun produit dans cette catégorie</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
