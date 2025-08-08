@extends('layouts.app')

@section('title', 'Détails de l\'Achat')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails de l'Achat #{{ $purchase->id }}</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de l'achat</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID:</strong> {{ $purchase->id }}
                    </div>
                    <div class="mb-3">
                        <strong>Date:</strong> {{ $purchase->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="mb-3">
                        <strong>Client:</strong> 
                        @if($purchase->client)
                            <a href="{{ route('clients.show', $purchase->client) }}">
                                {{ $purchase->client->nom_complet }} {{ $purchase->client->nom_complet }}
                            </a>
                        @else
                            <span class="text-muted">Client non spécifié</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Montant total:</strong> {{ number_format($purchase->total_amount, 2) }} FCFA
                    </div>
                    <div class="mb-3">
                        <strong>Méthode de paiement:</strong> 
                        @switch($purchase->payment_method)
                            @case('cash')
                                <span class="badge bg-success">Espèces</span>
                                @break
                            @case('wave')
                                <span class="badge bg-info">WAVE</span>
                                @break
                            @case('orange_money')
                                <span class="badge bg-warning text-dark">Orange Money</span>
                                @break
                            @default
                                <span class="badge bg-success">Espèces</span>
                        @endswitch
                    </div>
                    <div class="mb-3">
                        <strong>Statut:</strong>
                        @switch($purchase->status)
                            @case('completed')
                                <span class="badge bg-success">Complété</span>
                                @break
                            @case('pending')
                                <span class="badge bg-warning text-dark">En attente</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-danger">Annulé</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ $purchase->status }}</span>
                        @endswitch
                    </div>
                    @if($purchase->notes)
                        <div class="mb-3">
                            <strong>Notes:</strong> {{ $purchase->notes }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <strong>Créé le:</strong> {{ $purchase->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="mb-3">
                        <strong>Mis à jour le:</strong> {{ $purchase->updated_at->format('d/m/Y H:i') }}
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Retour
                        </a>
                        
                        @if($purchase->status !== 'cancelled')
                            <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cet achat ? Le stock sera restauré.');">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-ban mr-1"></i> Annuler l'achat
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Articles achetés</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Catégorie</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchase->items as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('products.show', $item->product) }}">
                                                {{ $item->product->name }}
                                            </a>
                                        </td>
                                        <td>{{ $item->product->category->name }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }} FCFA</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->subtotal, 2) }} FCFA</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Aucun article trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right font-weight-bold">Total:</td>
                                    <td class="font-weight-bold">{{ number_format($purchase->total_amount, 2) }} FCFA</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
