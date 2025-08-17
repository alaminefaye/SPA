@extends('layouts.app')

@section('title', 'Rapport des séances')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Rapports /</span> Rapport des Séances
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('reports.seances') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="report_type">Type de rapport</label>
                                    <select name="report_type" id="report_type" class="form-control" onchange="toggleCustomDate()">
                                        <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Journalier</option>
                                        <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                        <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                        <option value="annual" {{ $reportType == 'annual' ? 'selected' : '' }}>Annuel</option>
                                        <option value="custom" {{ $reportType == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 custom-date {{ $reportType == 'custom' ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="start_date">Date de début</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-3 custom-date {{ $reportType == 'custom' ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="end_date">Date de fin</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Filtrer</button>
                                </div>
                            </div>
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
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-calendar-check"></i> Séances totales</h5>
                            </div>
                            <h2 class="fw-bold text-primary">{{ $totalSeances }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-check-circle"></i> Séances terminées</h5>
                            </div>
                            <h2 class="fw-bold text-primary">{{ $totalTerminees }}</h2>
                            <p><span class="badge bg-primary">{{ $totalSeances > 0 ? round(($totalTerminees / $totalSeances) * 100, 1) : 0 }}%</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-times-circle"></i> Séances annulées</h5>
                            </div>
                            <h2 class="fw-bold text-danger">{{ $totalAnnulees }}</h2>
                            <p><span class="badge bg-danger">{{ $totalSeances > 0 ? round(($totalAnnulees / $totalSeances) * 100, 1) : 0 }}%</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-muted"><i class="fas fa-money-bill"></i> Revenu total</h5>
                            </div>
                            <h2 class="fw-bold text-primary">{{ number_format($totalRevenu, 0, ',', ' ') }} fr</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques et tableaux -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Statistiques par statut</h5>
                </div>
                <div class="card-body" style="min-height: 300px; border: 1px solid #eee; position: relative; padding: 20px;">
                    @if($totalTerminees > 0 || $totalAnnulees > 0 || $totalEnAttente > 0)
                        <canvas id="seancesStatusChart" height="200" style="display: block; width: 100%; max-height: 250px; border: 1px solid #f0f0f0; box-shadow: 0 0 5px rgba(0,0,0,0.05); border-radius: 4px;"></canvas>
                    @else
                        <div class="alert alert-warning">Aucune donnée disponible pour cette période.</div>
                    @endif
                    <!-- Débogage: Afficher les données directement -->
                    <div style="margin-top: 15px; font-size: 12px; color: #666;">
                        <strong>Données disponibles:</strong> 
                        <span class="badge bg-success">Terminées: {{ $totalTerminees }}</span>
                        <span class="badge bg-danger">Annulées: {{ $totalAnnulees }}</span>
                        <span class="badge bg-warning">En attente: {{ $totalEnAttente }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i> Séances par salon</h5>
                    <div class="card-header-action">
                        @can('export reports')
                        <a href="{{ route('reports.seances.excel') }}?report_type={{ $reportType }}&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a href="{{ route('reports.seances.pdf') }}?report_type={{ $reportType }}&start_date={{ $startDate }}&end_date={{ $endDate }}" class="btn btn-sm btn-danger">
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
                                    <th><i class="fas fa-building me-1"></i> Salon</th>
                                    <th><i class="fas fa-calendar-check me-1"></i> Total séances</th>
                                    <th><i class="fas fa-check-circle me-1 text-success"></i> Terminées</th>
                                    <th><i class="fas fa-times-circle me-1 text-danger"></i> Annulées</th>
                                    <th><i class="fas fa-money-bill me-1 text-success"></i> Revenu généré</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seancesBySalon as $salonStats)
                                <tr>
                                    <td><strong>{{ $salonStats['nom'] }}</strong></td>
                                    <td><span class="badge bg-primary">{{ $salonStats['total'] }}</span></td>
                                    <td><span class="badge bg-success">{{ $salonStats['terminees'] }}</span></td>
                                    <td><span class="badge bg-danger">{{ $salonStats['annulees'] }}</span></td>
                                    <td><span class="fw-bold text-success">{{ number_format($salonStats['revenu'], 0, ',', ' ') }} fr</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des séances -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Liste des séances</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="seancesTable">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i> #</th>
                                    <th><i class="fas fa-calendar-day me-1"></i> Date</th>
                                    <th><i class="fas fa-clock me-1"></i> Heure</th>
                                    <th><i class="fas fa-user me-1"></i> Client</th>
                                    <th><i class="fas fa-building me-1"></i> Salon</th>
                                    <th><i class="fas fa-spa me-1"></i> Prestations</th>
                                    <th><i class="fas fa-money-bill me-1"></i> Prix</th>
                                    <th><i class="fas fa-hourglass-half me-1"></i> Durée</th>
                                    <th><i class="fas fa-flag me-1"></i> Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seances as $seance)
                                <tr>
                                    <td class="fw-bold">{{ $seance->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($seance->date_seance)->format('d/m/Y') }}</td>
                                    <td>{{ $seance->heure_prevu }}</td>
                                    <td><strong>{{ $seance->client ? $seance->client->nom_complet : 'Client supprimé' }}</strong></td>
                                    <td><span class="badge bg-info text-dark">{{ $seance->salon ? $seance->salon->nom : 'Salon supprimé' }}</span></td>
                                    <td>
                                        @foreach($seance->prestations as $prestation)
                                            <span class="badge bg-primary">{{ $prestation->nom_prestation }}</span>{{ !$loop->last ? ' ' : '' }}
                                        @endforeach
                                    </td>
                                    <td><span class="fw-bold text-success">{{ number_format($seance->prix, 0, ',', ' ') }} fr</span></td>
                                    <td><span class="badge bg-secondary">{{ $seance->duree }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $seance->statut == 'terminee' ? 'success' : ($seance->statut == 'annulee' ? 'danger' : 'warning') }}">
                                            <i class="fas fa-{{ $seance->statut == 'terminee' ? 'check' : ($seance->statut == 'annulee' ? 'times' : 'clock') }} me-1"></i>
                                            {{ $seance->statut == 'terminee' ? 'Terminée' : ($seance->statut == 'annulee' ? 'Annulée' : 'En attente') }}
                                        </span>
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
<!-- Script de débogage pour voir si cette section est bien chargée -->
<script>
console.log('Script de débogage pour séances Chart.js chargé');
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    function toggleCustomDate() {
        const reportType = document.getElementById('report_type').value;
        const customDateFields = document.querySelectorAll('.custom-date');
        
        if (reportType === 'custom') {
            customDateFields.forEach(field => field.classList.remove('d-none'));
        } else {
            customDateFields.forEach(field => field.classList.add('d-none'));
        }
    }

    // Vérifier si jQuery est disponible et attendre que le document soit prêt
    if (typeof jQuery === 'undefined') {
        console.error('jQuery n\'est pas chargé!');
        document.body.innerHTML += '<div class="alert alert-danger m-3">Erreur: jQuery n\'est pas chargé. Le graphique ne peut pas être initialisé.</div>';
    } else {
        console.log('jQuery est correctement chargé, version:', jQuery.fn.jquery);
        
        jQuery(document).ready(function($) {
            console.log('Document prêt - Initialisation du graphique des séances');
            
            // Initialiser DataTables
            try {
                $('#seancesTable').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                    }
                });
                console.log('DataTable initialisée avec succès');
            } catch (dtError) {
                console.error('Erreur DataTables:', dtError);
            }
            
            // Vérifier si Chart.js est bien chargé
            if (typeof Chart === 'undefined') {
                console.error('Chart.js n\'est pas chargé');
                $('.card-body').prepend('<div class="alert alert-danger">Erreur: Chart.js n\'est pas chargé.</div>');
                return;
            }
            console.log('Chart.js correctement chargé');
            
            // Vérifier si l'élément canvas existe
            const canvas = document.getElementById('seancesStatusChart');
            if (!canvas) {
                console.error('Canvas #seancesStatusChart introuvable');
                $('.card-body:first').append('<div class="alert alert-danger">Erreur: Canvas #seancesStatusChart introuvable.</div>');
                return;
            }
            console.log('Canvas trouvé:', canvas);
            
            // Vérifier si des données sont disponibles
            const terminees = {{ $totalTerminees }};
            const annulees = {{ $totalAnnulees }};
            const enAttente = {{ $totalEnAttente }};
            
            console.log('Données disponibles:', { terminees, annulees, enAttente });
            
            if (terminees === 0 && annulees === 0 && enAttente === 0) {
                console.error('Aucune donnée disponible pour le graphique');
                $(canvas).after('<div class="alert alert-warning">Aucune donnée disponible pour cette période.</div>');
                return;
            }
            
            try {
                // Graphique des statuts
                const statusChart = canvas.getContext('2d');
                if (!statusChart) {
                    console.error('Impossible d\'obtenir le contexte 2d du canvas');
                    $(canvas).after('<div class="alert alert-danger">Erreur: Impossible d\'obtenir le contexte 2d du canvas.</div>');
                    return;
                }
                
                new Chart(statusChart, {
                    type: 'pie',
                    data: {
                        labels: ['Terminées', 'Annulées', 'En attente'],
                        datasets: [{
                            data: [terminees, annulees, enAttente],
                            backgroundColor: ['#FF57A8', '#dc3545', '#ffc107'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Graphique des séances créé avec succès');
            } catch (chartError) {
                console.error('Erreur lors de la création du graphique:', chartError);
                $(canvas).after('<div class="alert alert-danger">Erreur lors de la création du graphique: ' + chartError.message + '</div>');
            }
        });
    }
</script>
@endsection
