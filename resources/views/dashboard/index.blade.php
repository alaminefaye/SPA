@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('page-css')
<style>
  .dashboard-card {
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
  }
  .dashboard-card:hover {
    transform: translateY(-5px);
  }
  .card-icon {
    font-size: 2.5rem;
    opacity: 0.7;
  }
</style>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Administration /</span> Tableau de Bord
</h4>

<!-- Statistiques du jour -->
<div class="row mb-4">
  <div class="col-12 mb-3">
    <h5 class="text-primary"><i class="bx bx-calendar"></i> Statistiques du Jour</h5>
  </div>
  
  <!-- Séances du jour -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Séances Aujourd'hui</h5>
            <small class="text-muted">{{ Carbon\Carbon::now()->format('d/m/Y') }}</small>
          </div>
          <div class="avatar bg-primary p-2 rounded">
            <i class="bx bx-calendar-check text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-end">
          <div>
            <h2 class="mb-0">{{ $seancesAujourdhui }}</h2>
            <div>
              <span class="badge bg-success me-1">{{ $seancesPayantes }} Payées</span>
              <span class="badge bg-info">{{ $seancesGratuites }} Gratuites</span>
            </div>
          </div>
          <a href="{{ route('seances.index') }}" class="btn btn-sm btn-outline-primary">Détails</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Revenus du jour -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Revenus Aujourd'hui</h5>
            <small class="text-muted">{{ Carbon\Carbon::now()->format('d/m/Y') }}</small>
          </div>
          <div class="avatar bg-success p-2 rounded">
            <i class="bx bx-money text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-end">
          <div>
            <h2 class="mb-0">{{ number_format($revenuTotal, 0, ',', ' ') }} FCFA</h2>
            <div>
              <span class="badge bg-primary">{{ number_format($revenuPayant, 0, ',', ' ') }} FCFA Payé</span>
              @if($revenuTotal != $revenuPayant)
                <span class="badge bg-warning">{{ number_format($revenuTotal - $revenuPayant, 0, ',', ' ') }} FCFA Gratuit</span>
              @endif
            </div>
          </div>
          <a href="{{ route('seances.index') }}" class="btn btn-sm btn-outline-success">Détails</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Taux de conversion (séances gratuites vs payantes) -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Performance du Jour</h5>
            <small class="text-muted">{{ Carbon\Carbon::now()->format('d/m/Y') }}</small>
          </div>
          <div class="avatar bg-warning p-2 rounded">
            <i class="bx bx-trending-up text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4">
          @if($seancesAujourdhui > 0)
            <div class="progress mb-3" style="height: 8px">
              <div class="progress-bar bg-success" role="progressbar" 
                style="width: {{ ($seancesPayantes / $seancesAujourdhui) * 100 }}%" 
                aria-valuenow="{{ ($seancesPayantes / $seancesAujourdhui) * 100 }}" aria-valuemin="0" aria-valuemax="100">
              </div>
              <div class="progress-bar bg-info" role="progressbar" 
                style="width: {{ ($seancesGratuites / $seancesAujourdhui) * 100 }}%" 
                aria-valuenow="{{ ($seancesGratuites / $seancesAujourdhui) * 100 }}" aria-valuemin="0" aria-valuemax="100">
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <div><small>{{ round(($seancesPayantes / $seancesAujourdhui) * 100) }}% Payées</small></div>
              <div><small>{{ round(($seancesGratuites / $seancesAujourdhui) * 100) }}% Gratuites</small></div>
            </div>
          @else
            <div class="alert alert-info mb-0">Aucune séance aujourd'hui</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistiques globales -->
<div class="row mb-4">
  <div class="col-12 mb-3">
    <h5 class="text-primary"><i class="bx bx-chart"></i> Statistiques Globales</h5>
  </div>
  
  <!-- Clients -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Total Clients</h5>
            <small class="text-muted">Nombre total</small>
          </div>
          <div class="avatar bg-primary p-2 rounded">
            <i class="bx bx-user text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-end">
          <div>
            <h2 class="mb-0">{{ $totalClients }}</h2>
          </div>
          <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-primary">Détails</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Réservations -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Total Réservations</h5>
            <small class="text-muted">Nombre total</small>
          </div>
          <div class="avatar bg-success p-2 rounded">
            <i class="bx bx-calendar-plus text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-end">
          <div>
            <h2 class="mb-0">{{ $totalReservations }}</h2>
          </div>
          <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-success">Détails</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Total Séances -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Total Séances</h5>
            <small class="text-muted">Nombre total</small>
          </div>
          <div class="avatar bg-warning p-2 rounded">
            <i class="bx bx-calendar-event text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-end">
          <div>
            <h2 class="mb-0">{{ $totalSeances }}</h2>
          </div>
          <a href="{{ route('seances.index') }}" class="btn btn-sm btn-outline-warning">Détails</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistiques des produits -->
<div class="row mb-4">
  <div class="col-12 mb-3">
    <h5 class="text-primary"><i class="bx bx-package"></i> Gestion des Produits</h5>
  </div>
  
  <!-- Total Produits -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Total Produits</h5>
            <small class="text-muted">Nombre total</small>
          </div>
          <div class="avatar bg-info p-2 rounded">
            <i class="bx bx-box text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-end">
          <div>
            <h2 class="mb-0">{{ $totalProducts }}</h2>
          </div>
          <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-info">Détails</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Total Achats -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Total Achats</h5>
            <small class="text-muted">Nombre total</small>
          </div>
          <div class="avatar bg-success p-2 rounded">
            <i class="bx bx-cart text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-end">
          <div>
            <h2 class="mb-0">{{ $totalPurchases }}</h2>
          </div>
          <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-success">Détails</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Produits en stock bas -->
  <div class="col-lg-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-0">Alertes de Stock</h5>
            <small class="text-muted">Produits en stock bas</small>
          </div>
          <div class="avatar bg-danger p-2 rounded">
            <i class="bx bx-error-circle text-white card-icon"></i>
          </div>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-end">
          <div>
            <h2 class="mb-0">{{ $lowStockProducts }}</h2>
            <small class="text-danger">Nécessite attention</small>
          </div>
          <a href="{{ route('products.index') }}?filter=low-stock" class="btn btn-sm btn-outline-danger">Voir</a>
        </div>
      </div>
    </div>
  </div>
</div>

@if($recentLowStockProducts->count() > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger"><i class="bx bx-error-circle mr-1"></i> Produits en stock bas</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Produit</th>
                <th>Catégorie</th>
                <th>Stock actuel</th>
                <th>Seuil d'alerte</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentLowStockProducts as $product)
              <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td class="text-danger font-weight-bold">{{ $product->stock }}</td>
                <td>{{ $product->alert_threshold }}</td>
                <td>
                  <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">
                    <i class="bx bx-show"></i> Voir
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('vendors-js')
@endsection

@section('page-js')
@endsection
