@extends('layouts.app')

@section('title', 'Détails de l\'Achat')

@section('page-css')
<style>
    /* Styles pour les badges et étiquettes */
    .badge {
        font-size: 85%;
        padding: 0.4em 0.8em;
        font-weight: 600;
    }
    
    /* Styles pour les cartes */
    .card {
        border-radius: 0.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
    }
    
    /* Styles pour la chronologie */
    .timeline {
        list-style: none;
        padding: 0;
        position: relative;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 1.5rem;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
        padding-left: 3.5rem;
    }
    
    .timeline-marker {
        position: absolute;
        left: 0;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        text-align: center;
        color: white;
        line-height: 1.5rem;
        font-size: 0.75rem;
    }
    
    .timeline-content {
        padding: 0.5rem 0;
    }
    
    .timeline-title {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    /* Styles pour les infos client */
    .client-avatar {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #f8f9fc;
        color: #4e73df;
    }
    
    /* Style pour le tableau des produits */
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    /* Style pour les boutons d'action */
    .btn {
        border-radius: 0.35rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart text-primary me-2"></i> Détails de l'Achat #{{ $purchase->id }}
        </h1>
        <div>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list mr-1"></i> Voir tous les achats
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <!-- Statut de l'achat en grand -->    
    <div class="row mb-4">
        <div class="col-12">
            @switch($purchase->status)
                @case('completed')
                    <div class="card bg-success text-white shadow">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle fa-2x mr-2"></i>
                                <div>
                                    <h6 class="m-0 font-weight-bold">Achat complété</h6>
                                    <small>Cet achat a été finalisé avec succès.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break
                @case('pending')
                    <div class="card bg-warning text-dark shadow">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-clock fa-2x mr-2"></i>
                                <div>
                                    <h6 class="m-0 font-weight-bold">Achat en attente</h6>
                                    <small>Cet achat est en cours de traitement.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break
                @case('cancelled')
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-ban fa-2x mr-2"></i>
                                <div>
                                    <h6 class="m-0 font-weight-bold">Achat annulé</h6>
                                    <small>Cet achat a été annulé et les stocks ont été restaurés.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break
                @default
                    <div class="card bg-secondary text-white shadow">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-question-circle fa-2x mr-2"></i>
                                <div>
                                    <h6 class="m-0 font-weight-bold">État: {{ $purchase->status }}</h6>
                                    <small>Le statut de cet achat est inconnu.</small>
                                </div>
                            </div>
                        </div>
                    </div>
            @endswitch
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Carte d'informations client -->            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i> Information Client
                    </h6>
                </div>
                <div class="card-body">
                    <div class="client-info">
                        @if($purchase->client)
                            <div class="text-center mb-4">
                                <div class="client-avatar mb-3">
                                    <i class="fas fa-user-circle fa-4x text-gray-300"></i>
                                </div>
                                <h5 class="client-name font-weight-bold">
                                    <a href="{{ route('clients.show', $purchase->client) }}" class="text-decoration-none">
                                        {{ $purchase->client->nom_complet }}
                                    </a>
                                </h5>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-envelope mr-1"></i> {{ $purchase->client->adresse_mail ?? 'Aucun email' }}
                                </p>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-phone mr-1"></i> {{ $purchase->client->numero_telephone ?? 'Aucun téléphone' }}
                                </p>
                                <p class="mb-3 mt-2">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-star mr-1"></i> {{ $purchase->client->points ?? '0' }} Points
                                    </span>
                                </p>
                                <a href="{{ route('clients.show', $purchase->client) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt mr-1"></i> Voir le profil complet
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-slash fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted mb-0">Aucun client associé à cet achat</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Carte des informations de paiement -->            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-money-bill-wave mr-2"></i> Détails du paiement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12 text-center">
                            <h3 class="text-primary font-weight-bold mb-3">{{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA</h3>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span><i class="fas fa-hashtag mr-2 text-gray-600"></i>ID:</span>
                                <span class="font-weight-bold">{{ $purchase->id }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span><i class="far fa-calendar-alt mr-2 text-gray-600"></i>Date:</span>
                                <span class="font-weight-bold">{{ $purchase->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <span><i class="fas fa-credit-card mr-2 text-gray-600"></i>Méthode:</span>
                                <span>
                                    @switch($purchase->payment_method)
                                        @case('cash')
                                            <span class="badge bg-success"><i class="fas fa-money-bill mr-1"></i> Espèces</span>
                                            @break
                                        @case('wave')
                                            <span class="badge bg-info"><i class="fas fa-mobile-alt mr-1"></i> WAVE</span>
                                            @break
                                        @case('orange_money')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-mobile-alt mr-1"></i> Orange Money</span>
                                            @break
                                        @default
                                            <span class="badge bg-success"><i class="fas fa-money-bill mr-1"></i> Espèces</span>
                                    @endswitch
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="far fa-clock mr-2 text-gray-600"></i>Mis à jour:</span>
                                <span>{{ $purchase->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($purchase->notes)
                        <div class="card bg-light mt-3">
                            <div class="card-body py-2 px-3">
                                <h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-sticky-note mr-2"></i>Notes:</h6>
                                <p class="card-text">{{ $purchase->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-primary m-1">
                            <i class="fas fa-edit mr-1"></i> Modifier
                        </a>
                        
                        @if($purchase->status !== 'cancelled')
                            <a href="{{ route('purchases.ticket', $purchase) }}" class="btn btn-success m-1" target="_blank">
                                <i class="fas fa-print mr-1"></i> Ticket
                            </a>
                            
                            <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cet achat ? Le stock sera restauré.');">
                                @csrf
                                <button type="submit" class="btn btn-danger m-1">
                                    <i class="fas fa-ban mr-1"></i> Annuler
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shopping-basket mr-2"></i> Articles achetés
                    </h6>
                    <span class="badge bg-primary rounded-pill">
                        {{ $purchase->items->count() }} {{ Str::plural('article', $purchase->items->count()) }}
                    </span>
                </div>
                <div class="card-body">
                    @if($purchase->items->count() > 0)
                        <div class="table-responsive mb-4">
                            <table class="table table-hover table-striped" width="100%" cellspacing="0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 35%">Produit</th>
                                        <th>Catégorie</th>
                                        <th>Prix unitaire</th>
                                        <th>Quantité</th>
                                        <th>Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->image)
                                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="product-thumbnail mr-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                    @else
                                                        <div class="product-thumbnail-placeholder mr-2" style="width: 40px; height: 40px; background-color: #f8f9fc; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-box text-gray-300"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <a href="{{ route('products.show', $item->product) }}" class="font-weight-bold text-decoration-none">
                                                            {{ $item->product->name }}
                                                        </a>
                                                        <div class="small text-muted">
                                                            ID: #{{ $item->product->id }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-white">
                                                    {{ $item->product->category->name }}
                                                </span>
                                            </td>
                                            <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="text-right font-weight-bold">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="4" class="text-right font-weight-bold">Sous-total:</td>
                                        <td class="text-right">{{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    <!-- Calculer la TVA si nécessaire -->
                                    <tr>
                                        <td colspan="4" class="text-right font-weight-bold">Total:</td>
                                        <td class="text-right font-weight-bold text-primary">{{ number_format($purchase->total_amount, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Aucun article n'est associé à cet achat</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Section historique d'activité -->            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i> Historique d'activité
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-marker bg-success">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Achat créé</h6>
                                <p class="timeline-text text-muted">{{ $purchase->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </li>
                        @if($purchase->created_at != $purchase->updated_at)
                            <li class="timeline-item">
                                <div class="timeline-marker bg-primary">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Achat modifié</h6>
                                    <p class="timeline-text text-muted">{{ $purchase->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </li>
                        @endif
                        @if($purchase->status == 'cancelled')
                            <li class="timeline-item">
                                <div class="timeline-marker bg-danger">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Achat annulé</h6>
                                    <p class="timeline-text text-muted">{{ $purchase->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
