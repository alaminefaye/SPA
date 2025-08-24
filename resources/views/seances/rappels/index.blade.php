@extends('layouts.app')

@section('title', 'Rappels de Rendez-vous')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('seances.index') }}">Séances</a> /</span> Rappels de Rendez-vous
</h4>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Filtres</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('rappels.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Période</label>
                        <select class="form-select" name="periode" onchange="this.form.submit()">
                            <option value="aujourd'hui" {{ $periode == "aujourd'hui" ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="semaine" {{ $periode == "semaine" ? 'selected' : '' }}>Cette semaine</option>
                            <option value="mois" {{ $periode == "mois" ? 'selected' : '' }}>Ce mois</option>
                            <option value="tous" {{ $periode == "tous" ? 'selected' : '' }}>Tous les rappels</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Recherche par nom/téléphone</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Rechercher..." name="search" value="{{ $search ?? '' }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bx bx-search"></i>
                            </button>
                            @if($search)
                                <a href="{{ route('rappels.index', ['periode' => $periode]) }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des rappels de rendez-vous</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr class="text-nowrap">
                            <th>Client</th>
                            <th>Date prévue</th>
                            <th>Heure</th>
                            <th>Statut</th>
                            <th>Séance d'origine</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rappels as $rappel)
                            <tr>
                                <td>
                                    <strong>{{ $rappel->client->nom_complet }}</strong><br>
                                    <small>{{ $rappel->client->numero_telephone }}</small>
                                </td>
                                <td>
                                    {{ $rappel->date_prevue->format('d/m/Y') }}
                                    @if($rappel->estPourAujourdhui())
                                        <span class="badge bg-danger">Aujourd'hui</span>
                                    @elseif($rappel->estPourDansDeuxJours())
                                        <span class="badge bg-danger">Dans 2 jours</span>
                                    @elseif($rappel->estPourCetteSemaine())
                                        <span class="badge bg-warning">Cette semaine</span>
                                    @endif
                                </td>
                                <td>{{ $rappel->heure_prevue->format('H:i') }}</td>
                                <td>
                                    @if($rappel->statut == 'en_attente')
                                        <span class="badge bg-warning">En attente</span>
                                    @elseif($rappel->statut == 'confirme')
                                        <span class="badge bg-success">Confirmé</span>
                                    @elseif($rappel->statut == 'annule')
                                        <span class="badge bg-danger">Annulé</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rappel->seance)
                                        <a href="{{ route('seances.show', $rappel->seance->id) }}" target="_blank">
                                            Séance #{{ $rappel->seance->id }}
                                            <i class="bx bx-link-external"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rappel->statut == 'en_attente')
                                        <!-- Bouton WhatsApp -->
                                        <a class="btn btn-sm btn-success" href="{{ $rappel->getWhatsAppUrl() }}" target="_blank" title="Envoyer rappel WhatsApp">
                                            <i class="bx bxl-whatsapp"></i>
                                        </a>
                                        
                                        <!-- Menu déroulant pour les autres actions -->
                                        <div class="dropdown d-inline-block">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item text-success" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('confirm-form-{{ $rappel->id }}').submit();">
                                                    <i class="bx bx-check me-1"></i> Confirmer
                                                </a>
                                                <form id="confirm-form-{{ $rappel->id }}" action="{{ route('rappels.confirmer', $rappel->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                                
                                                <a class="dropdown-item text-danger" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('cancel-form-{{ $rappel->id }}').submit();">
                                                    <i class="bx bx-x me-1"></i> Annuler
                                                </a>
                                                <form id="cancel-form-{{ $rappel->id }}" action="{{ route('rappels.annuler', $rappel->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                                
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('rappels.creer-seance', $rappel->id) }}">
                                                    <i class="bx bx-calendar-plus me-1"></i> Créer une séance
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-center">
                                        <i class="bx bx-calendar-x text-secondary mb-2" style="font-size: 4rem;"></i>
                                        <p class="mb-0">Aucun rappel de rendez-vous trouvé pour cette période</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($rappels->count() > 0)
                <div class="card-footer pb-0">
                    <div class="d-flex justify-content-center">
                        {{ $rappels->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
