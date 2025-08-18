@extends('layouts.app')

@section('title', 'Détails de l\'employé')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails de l'employé</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employés</a></li>
        <li class="breadcrumb-item active">Détails</li>
    </ol>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Informations personnelles
                </div>
                <div class="card-body text-center">
                    @if($employee->photo)
                    <img src="{{ Storage::url($employee->photo) }}" alt="Photo de {{ $employee->prenom }} {{ $employee->nom }}" class="rounded-circle img-fluid mb-3" style="max-height: 200px;">
                    @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px; color: white; font-size: 48px;">
                        <i class="fas fa-user"></i>
                    </div>
                    @endif
                    
                    <h4>{{ $employee->prenom }} {{ $employee->nom }}</h4>
                    <h5 class="text-muted">{{ $employee->poste }}</h5>
                    <span class="badge {{ $employee->actif ? 'bg-success' : 'bg-danger' }} mb-3">
                        {{ $employee->actif ? 'Actif' : 'Inactif' }}
                    </span>
                    
                    <div class="mt-3 d-flex justify-content-center">
                        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('admin.employees.toggle-status', $employee) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn {{ $employee->actif ? 'btn-warning' : 'btn-success' }}" 
                                    title="{{ $employee->actif ? 'Désactiver' : 'Activer' }}">
                                <i class="fas {{ $employee->actif ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                {{ $employee->actif ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-id-card me-1"></i>
                    Coordonnées
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Numéro de téléphone:</div>
                        <div class="col-md-8">{{ $employee->numero_telephone }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email:</div>
                        <div class="col-md-8">
                            @if($employee->email)
                                <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                            @else
                                <span class="text-muted">Non renseigné</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Adresse:</div>
                        <div class="col-md-8">
                            @if($employee->adresse)
                                {{ $employee->adresse }}
                            @else
                                <span class="text-muted">Non renseignée</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-briefcase me-1"></i>
                    Informations professionnelles
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Salon:</div>
                        <div class="col-md-8">
                            @if($employee->salon)
                                <a href="{{ route('salons.show', $employee->salon) }}">{{ $employee->salon->nom }}</a>
                            @else
                                <span class="text-muted">Non assigné</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Date d'embauche:</div>
                        <div class="col-md-8">{{ $employee->date_embauche->format('d/m/Y') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Salaire:</div>
                        <div class="col-md-8">
                            @if($employee->salaire)
                                {{ number_format($employee->salaire, 0, ',', ' ') }} FCFA
                            @else
                                <span class="text-muted">Non renseigné</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Spécialités:</div>
                        <div class="col-md-8">
                            @if($employee->specialites)
                                @php
                                    $specialites = explode(',', $employee->specialites);
                                @endphp
                                @foreach($specialites as $specialite)
                                    <span class="badge bg-info me-1">{{ trim($specialite) }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Non renseignées</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Date de naissance:</div>
                        <div class="col-md-8">
                            @if($employee->date_naissance)
                                {{ $employee->date_naissance->format('d/m/Y') }}
                                ({{ $employee->date_naissance->age }} ans)
                            @else
                                <span class="text-muted">Non renseignée</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($employee->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-sticky-note me-1"></i>
                    Notes
                </div>
                <div class="card-body">
                    {{ $employee->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-history me-1"></i>
            Historique des activités
        </div>
        <div class="card-body">
            @if($activities && count($activities) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Description</th>
                                <th>Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($activity->causer)
                                            {{ $activity->causer->name }}
                                        @else
                                            Système
                                        @endif
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                    <td>
                                        @if($activity->properties && count($activity->properties) > 0)
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#activityModal{{ $activity->id }}">
                                                Voir les détails
                                            </button>
                                            
                                            <div class="modal fade" id="activityModal{{ $activity->id }}" tabindex="-1" aria-labelledby="activityModalLabel{{ $activity->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="activityModalLabel{{ $activity->id }}">Détails de l'activité</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @if(isset($activity->properties['attributes']))
                                                                <h6>Nouvelles valeurs:</h6>
                                                                <pre class="bg-light p-3">{{ json_encode($activity->properties['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            @endif
                                                            
                                                            @if(isset($activity->properties['old']))
                                                                <h6>Anciennes valeurs:</h6>
                                                                <pre class="bg-light p-3">{{ json_encode($activity->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Aucun détail</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Aucune activité enregistrée pour cet employé.</p>
            @endif
        </div>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
        <div>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash me-1"></i> Supprimer
            </button>
        </div>
    </div>
    
    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cet employé? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
