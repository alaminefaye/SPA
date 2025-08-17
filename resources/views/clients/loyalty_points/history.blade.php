@extends('layouts.app')

@section('title', 'Historique des points de fidélité')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Points de fidélité /</span> Historique
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Historique des points de fidélité - {{ $client->nom_complet }}</h5>
                    <a href="{{ route('loyalty-points.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Retour à la liste
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="client-info p-3 border rounded">
                                <h6>Informations du client</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nom :</strong> {{ $client->nom_complet }}</p>
                                        <p><strong>Téléphone :</strong> {{ $client->numero_telephone }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Email :</strong> {{ $client->adresse_mail ?: 'Non renseigné' }}</p>
                                        <p><strong>Points actuels :</strong> <span class="badge bg-primary">{{ $client->points }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Points avant</th>
                                    <th>Points après</th>
                                    <th>Différence</th>
                                    <th>Raison</th>
                                    <th>Effectué par</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $action = isset($activity->properties['action']) ? $activity->properties['action'] : null;
                                            $actionLabel = '';
                                            $actionClass = '';
                                            
                                            switch($action) {
                                                case 'add':
                                                    $actionLabel = 'Ajout';
                                                    $actionClass = 'success';
                                                    break;
                                                case 'remove':
                                                    $actionLabel = 'Retrait';
                                                    $actionClass = 'danger';
                                                    break;
                                                case 'set':
                                                    $actionLabel = 'Redéfinition';
                                                    $actionClass = 'warning';
                                                    break;
                                                default:
                                                    if ($activity->properties['original_points'] < $activity->properties['new_points']) {
                                                        $actionLabel = 'Ajout';
                                                        $actionClass = 'success';
                                                    } elseif ($activity->properties['original_points'] > $activity->properties['new_points']) {
                                                        $actionLabel = 'Retrait';
                                                        $actionClass = 'danger';
                                                    } else {
                                                        $actionLabel = 'Modification';
                                                        $actionClass = 'info';
                                                    }
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $actionClass }}">{{ $actionLabel }}</span>
                                    </td>
                                    <td>{{ $activity->properties['original_points'] ?? '-' }}</td>
                                    <td>{{ $activity->properties['new_points'] ?? '-' }}</td>
                                    <td>
                                        @php
                                            $originalPoints = $activity->properties['original_points'] ?? 0;
                                            $newPoints = $activity->properties['new_points'] ?? 0;
                                            $diff = $newPoints - $originalPoints;
                                            $diffClass = $diff > 0 ? 'success' : ($diff < 0 ? 'danger' : 'secondary');
                                        @endphp
                                        <span class="badge bg-{{ $diffClass }}">
                                            {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                        </span>
                                    </td>
                                    <td>{{ $activity->properties['raison'] ?? 'Non spécifiée' }}</td>
                                    <td>
                                        @if($activity->causer)
                                            {{ $activity->causer->name }}
                                        @else
                                            Système
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Aucun historique disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 px-2">
                        {{ $activities->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
