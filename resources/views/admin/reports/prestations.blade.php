@extends('layouts.app')

@section('title', 'Rapport des Prestations')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Rapports /</span> Rapport des Prestations
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('reports.prestations') }}" method="GET" class="row">
                        <div class="form-group col-md-3 mb-3">
                            <label for="report_type" class="form-label"><i class="fas fa-calendar me-2"></i>Type de rapport</label>
                            <select name="report_type" id="report_type" class="form-select" onchange="toggleDateInputs(this.value)">
                                <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Journalier</option>
                                <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                <option value="annual" {{ $reportType == 'annual' ? 'selected' : '' }}>Annuel</option>
                                <option value="custom" {{ $reportType == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                            </select>
                        </div>

                        <div id="date_inputs" class="row col-md-6" style="{{ $reportType == 'custom' ? '' : 'display: none;' }}">
                            <div class="form-group col-md-6 mb-3">
                                <label for="start_date" class="form-label"><i class="fas fa-calendar-minus me-2"></i>Date de début</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="end_date" class="form-label"><i class="fas fa-calendar-plus me-2"></i>Date de fin</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                        </div>

                        <div class="form-group col-md-3 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i> Filtrer
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
                                <h5 class="text-muted"><i class="fas fa-clipboard-list me-2"></i> Total Prestations</h5>
                            </div>
                            <h2 class="fw-bold text-primary">{{ $totalPrestations }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-money-bill-wave me-2"></i> Revenu Total</h5>
                            </div>
                            <h2 class="fw-bold text-success">{{ number_format($totalRevenue, 0, ',', ' ') }} fr</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-calendar-check me-2"></i> Séances Terminées</h5>
                            </div>
                            <h2 class="fw-bold text-info">{{ $seances->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h4><i class="fas fa-chart-bar me-2"></i> Top 5 Prestations</h4>
                </div>
                <div class="card-body p-3">
                    <canvas id="prestationsChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-list-ol me-2"></i> Prestations par popularité</h4>
                    <div class="card-header-action">
                        @can('export reports')
                        <a href="{{ route('reports.prestations.excel') }}?report_type={{ $reportType }}&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a href="{{ route('reports.prestations.pdf') }}?report_type={{ $reportType }}&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-danger btn-sm">
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
                                    <th><i class="fas fa-spa me-1"></i> Prestation</th>
                                    <th><i class="fas fa-sort-numeric-up me-1"></i> Nb. fois réalisée</th>
                                    <th><i class="fas fa-money-bill-wave me-1"></i> Revenu généré</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prestationsCount as $prestation)
                                <tr class="align-middle">
                                    <td><strong>{{ $prestation['nom'] }}</strong></td>
                                    <td><span class="badge bg-info">{{ $prestation['count'] }}</span></td>
                                    <td><span class="fw-bold text-success">{{ number_format($prestation['revenue'], 0, ',', ' ') }} fr</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h4><i class="fas fa-calendar-alt me-2"></i> Détails des Séances</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="seances-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar-day me-1"></i> Date</th>
                                    <th><i class="fas fa-user me-1"></i> Client</th>
                                    <th><i class="fas fa-store me-1"></i> Salon</th>
                                    <th><i class="fas fa-spa me-1"></i> Prestations</th>
                                    <th><i class="fas fa-money-bill-wave me-1"></i> Prix</th>
                                    <th><i class="fas fa-info-circle me-1"></i> Statut</th>
                                    <th><i class="fas fa-cog me-1"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seances as $seance)
                                <tr class="align-middle">
                                    <td>{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</td>
                                    <td><strong>{{ $seance->client->nom_complet }}</strong></td>
                                    <td>{{ $seance->salon->nom }}</td>
                                    <td>
                                        @foreach($seance->prestations as $prestation)
                                            <span class="badge bg-info mb-1 d-inline-block">{{ $prestation->nom_prestation }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($seance->is_free)
                                            <span class="badge bg-success">Gratuite</span>
                                        @else
                                            <span class="fw-bold text-success">{{ number_format($seance->prix, 0, ',', ' ') }} fr</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($seance->statut == 'terminee')
                                            <span class="badge bg-success">{{ ucfirst($seance->statut) }}</span>
                                        @elseif($seance->statut == 'annulee')
                                            <span class="badge bg-danger">{{ ucfirst($seance->statut) }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ ucfirst($seance->statut) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('seances.show', $seance->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleDateInputs(value) {
        const dateInputs = document.getElementById('date_inputs');
        if (value === 'custom') {
            dateInputs.style.display = '';
        } else {
            dateInputs.style.display = 'none';
        }
    }

    // Préparation des données pour le graphique des prestations les plus populaires
    document.addEventListener('DOMContentLoaded', function() {
        const prestationsData = @json(array_slice($prestationsCount, 0, 5));
        const labels = Object.values(prestationsData).map(p => p.nom);
        const data = Object.values(prestationsData).map(p => p.count);
        const revenues = Object.values(prestationsData).map(p => p.revenue);
        
        // Configuration du graphique
        const ctx = document.getElementById('prestationsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de prestations',
                    data: data,
                    backgroundColor: 'rgba(63, 114, 155, 0.7)',
                    borderColor: 'rgba(63, 114, 155, 1)',
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
        $('#seances-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    });
</script>
@endpush
