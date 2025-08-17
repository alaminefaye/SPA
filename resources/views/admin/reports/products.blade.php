@extends('layouts.app')

@section('title', 'Rapport des Ventes de Produits')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Rapports /</span> Rapport des Ventes de Produits
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('reports.products') }}" method="GET" class="row">
                        <div class="form-group col-md-3">
                            <label for="report_type">Type de rapport</label>
                            <select name="report_type" id="report_type" class="form-control" onchange="toggleDateInputs(this.value)">
                                <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Journalier</option>
                                <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                <option value="annual" {{ $reportType == 'annual' ? 'selected' : '' }}>Annuel</option>
                                <option value="custom" {{ $reportType == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                            </select>
                        </div>

                        <div id="date_inputs" class="row col-md-6" style="{{ $reportType == 'custom' ? '' : 'display: none;' }}">
                            <div class="form-group col-md-6">
                                <label for="start_date">Date de début</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="end_date">Date de fin</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Statistiques globales -->
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-shopping-cart"></i> Total des Ventes</h5>
                            </div>
                            <h2 class="fw-bold text-primary">{{ $totalSales }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-money-bill-wave"></i> Revenu Total</h5>
                            </div>
                            <h2 class="fw-bold text-primary">{{ number_format($totalRevenue, 0, ',', ' ') }} fr</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-box-open"></i> Produits Vendus</h5>
                            </div>
                            <h2 class="fw-bold text-info">{{ $totalProducts }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h4><i class="fas fa-chart-bar me-2"></i> Top 5 Produits Vendus</h4>
                </div>
                <div class="card-body p-3" style="min-height: 250px; border: 1px solid #eee;">
                    @if(count($productStats) > 0)
                        <canvas id="productsChart" height="200" style="display: block; width: 100%;"></canvas>
                    @else
                        <div class="alert alert-warning">Aucune donnée disponible pour cette période.</div>
                    @endif
                    <!-- Débogage: Afficher les données directement -->
                    <div style="margin-top: 15px; font-size: 12px; color: #666;">
                        <strong>Données disponibles:</strong> {{ count($productStats) }} produits
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-list-ol me-2"></i> Produits par ventes</h4>
                    <div class="card-header-action">
                        @can('export reports')
                        <a href="{{ route('reports.products.excel') }}?report_type={{ $reportType }}&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a href="{{ route('reports.products.pdf') }}?report_type={{ $reportType }}&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-box me-1"></i> Produit</th>
                                    <th><i class="fas fa-shopping-basket me-1"></i> Quantité vendue</th>
                                    <th><i class="fas fa-money-bill-wave me-1"></i> Revenu généré</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productStats as $product)
                                <tr>
                                    <td><strong>{{ $product['name'] }}</strong></td>
                                    <td><span class="badge bg-info">{{ $product['quantity'] }}</span></td>
                                    <td><span class="fw-bold text-success">{{ number_format($product['revenue'], 0, ',', ' ') }} fr</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h4><i class="fas fa-history me-2"></i> Détails des Ventes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="sales-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar-day me-1"></i> Date</th>
                                    <th><i class="fas fa-user me-1"></i> Client</th>
                                    <th><i class="fas fa-box me-1"></i> Produits</th>
                                    <th><i class="fas fa-money-bill-wave me-1"></i> Montant</th>
                                    <th><i class="fas fa-credit-card me-1"></i> Méthode</th>
                                    <th><i class="fas fa-info-circle me-1"></i> Statut</th>
                                    <th><i class="fas fa-cog me-1"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $sale)
                                <tr class="align-middle">
                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</td>
                                    <td><strong>{{ $sale->client->nom_complet }}</strong></td>
                                    <td>
                                        @foreach($sale->products as $product)
                                            <span class="badge bg-info mb-1 d-inline-block">{{ $product->name }} ({{ $product->pivot->quantity }})</span>
                                        @endforeach
                                    </td>
                                    <td><span class="fw-bold text-success">{{ number_format($sale->total_amount, 0, ',', ' ') }} fr</span></td>
                                    <td>{{ ucfirst($sale->payment_method) }}</td>
                                    <td>
                                        @if($sale->status == 'completed')
                                            <span class="badge bg-success">Terminée</span>
                                        @elseif($sale->status == 'cancelled')
                                            <span class="badge bg-danger">Annulée</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ ucfirst($sale->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.show', $sale->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
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
</div>
@endsection

@section('page-js')
<!-- Assurez-vous que Chart.js est correctement chargé -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- Script de débogage pour voir si cette section est bien chargée -->
<script>
console.log('Script de débogage Chart.js chargé');
</script>
<script>
    function toggleDateInputs(value) {
        const dateInputs = document.getElementById('date_inputs');
        if (value === 'custom') {
            dateInputs.style.display = '';
        } else {
            dateInputs.style.display = 'none';
        }
    }

    // Utiliser jQuery pour le chargement et la vérification que la page est complètement chargée
    $(document).ready(function() {
        console.log('Document prêt - Initialisation du graphique');
        
        // Vérifier si Chart.js est bien chargé
        if (typeof Chart === 'undefined') {
            console.error('Chart.js n\'est pas chargé');
            $('.card-body.p-3').prepend('<div class="alert alert-danger">Erreur: Chart.js n\'est pas chargé.</div>');
            return;
        } else {
            console.log('Chart.js correctement chargé');
        }

        // Vérifier si l'élément canvas existe
        const canvas = document.getElementById('productsChart');
        if (!canvas) {
            console.error('Canvas #productsChart introuvable');
            return;
        }
        console.log('Canvas trouvé:', canvas);

        // Débogage: afficher les statistiques des produits dans la console
        const productStats = @json(array_slice($productStats, 0, 5));
        console.log('Données des produits:', productStats);
        
        // Vérifier si productStats contient des données
        if (!productStats || Object.keys(productStats).length === 0) {
            console.error('Aucune donnée de produit disponible pour le graphique');
            $(canvas).after('<div class="alert alert-warning">Aucune donnée disponible pour cette période.</div>');
            return;
        }
        
        const labels = Object.values(productStats).map(p => p.name);
        const data = Object.values(productStats).map(p => p.quantity);
        const revenues = Object.values(productStats).map(p => p.revenue);
        
        console.log('Labels:', labels);
        console.log('Données:', data);
        console.log('Revenus:', revenues);
        
        // Configuration du graphique
        const ctx = document.getElementById('productsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantité vendue',
                    data: data,
                    backgroundColor: 'rgba(255, 87, 168, 0.7)',
                    borderColor: 'rgba(255, 87, 168, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const index = context.dataIndex;
                                return `Revenu: ${new Intl.NumberFormat('fr-FR').format(revenues[index])} FCFA`;
                            }
                        }
                    }
                }
            }
        });
        
        // Initialiser la table de données avec DataTables
        $('#sales-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    });
</script>
@endsection
