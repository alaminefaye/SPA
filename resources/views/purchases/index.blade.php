@extends('layouts.app')

@section('title', 'Gestion des Achats')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestion des Achats</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des achats</h6>
            <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouvel achat
            </a>
        </div>
        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form action="{{ route('purchases.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Rechercher un achat..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">Rechercher</button>
                        @if(request()->has('search'))
                            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
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
                            <th>Date</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Méthode de paiement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($purchase->client)
                                        <a href="{{ route('clients.show', $purchase->client) }}">
                                            {{ $purchase->client->nom_complet }}
                                        </a>
                                    @else
                                        <span class="text-muted">Client non spécifié</span>
                                    @endif
                                </td>
                                <td>{{ number_format($purchase->total_amount, 2) }} FCFA</td>
                                <td>
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
                                </td>
                                <td>
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
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-icon btn-info"
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="Voir détails">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        @if($purchase->status !== 'cancelled')
                                            <form action="{{ route('purchases.cancel', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cet achat ? Le stock sera restauré.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon btn-danger"
                                                       data-bs-toggle="tooltip" data-bs-placement="top" title="Annuler l'achat">
                                                    <i class="bx bx-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucun achat trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    $(document).ready(function() {
        // Initialisation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Initialisation du DataTable
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    });
</script>
@endsection
