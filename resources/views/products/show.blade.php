@extends('layouts.app')

@section('title', 'Détails du Produit')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails du Produit: {{ $product->name }}</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du produit</h6>
                </div>
                <div class="card-body">
                    @if($product->image)
                        <div class="text-center mb-4">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 200px">
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>ID:</strong> {{ $product->id }}
                    </div>
                    <div class="mb-3">
                        <strong>Nom:</strong> {{ $product->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Catégorie:</strong> <a href="{{ route('product-categories.show', $product->category) }}">{{ $product->category->name }}</a>
                    </div>
                    <div class="mb-3">
                        <strong>Description:</strong> {{ $product->description ?? 'Non spécifiée' }}
                    </div>
                    <div class="mb-3">
                        <strong>Prix:</strong> {{ number_format($product->price, 2) }} FCFA
                    </div>
                    <div class="mb-3">
                        <strong>Stock actuel:</strong> 
                        <span class="{{ $product->isLowStock() ? 'text-danger font-weight-bold' : '' }}">
                            {{ $product->stock }}
                            @if($product->isLowStock())
                                <i class="fas fa-exclamation-triangle text-danger ml-1" title="Stock bas"></i>
                            @endif
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Seuil d'alerte:</strong> {{ $product->alert_threshold }}
                    </div>
                    <div class="mb-3">
                        <strong>Créé le:</strong> {{ $product->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="mb-3">
                        <strong>Mis à jour le:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i> Modifier
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Gestion du stock</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.updateStock', $product) }}" method="POST" class="form-inline mb-4">
                        @csrf
                        <div class="form-group mr-2">
                            <label for="stock" class="mr-2">Nouveau stock:</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="{{ $product->stock }}" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Mettre à jour le stock
                        </button>
                    </form>
                    
                    <hr>
                    
                    <h6 class="font-weight-bold">Ajouter ce produit à un achat</h6>
                    <a href="{{ route('purchases.create') }}" class="btn btn-success mt-2">
                        <i class="fas fa-shopping-cart mr-1"></i> Créer un achat
                    </a>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des achats</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($product->purchaseItems as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($item->purchase->client)
                                                <a href="{{ route('clients.show', $item->purchase->client) }}">
                                                    {{ $item->purchase->client->nom }} {{ $item->purchase->client->prenom }}
                                                </a>
                                            @else
                                                <span class="text-muted">Client non spécifié</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }} FCFA</td>
                                        <td>{{ number_format($item->subtotal, 2) }} FCFA</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Aucun historique d'achat pour ce produit</td>
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
