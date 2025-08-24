@extends('layouts.app')

@section('title', 'Détail de la suggestion')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion / Suggestions /</span> Détail
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">{{ $feedback->sujet }}</h5>
                    <div>
                        @if($feedback->is_priority)
                            <span class="badge bg-danger">Prioritaire</span>
                        @endif
                        @if(!$feedback->is_read)
                            <span class="badge bg-info">Nouveau</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if($feedback->satisfaction_rating)
                    <div class="mb-4">
                        <h6 class="fw-semibold">Niveau de satisfaction :</h6>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="font-size: 2rem">
                                    @switch($feedback->satisfaction_rating)
                                        @case(1)
                                            <span title="Très insatisfait" data-bs-toggle="tooltip">😠</span>
                                            @break
                                        @case(2)
                                            <span title="Insatisfait" data-bs-toggle="tooltip">🙁</span>
                                            @break
                                        @case(3)
                                            <span title="Neutre" data-bs-toggle="tooltip">😐</span>
                                            @break
                                        @case(4)
                                            <span title="Satisfait" data-bs-toggle="tooltip">🙂</span>
                                            @break
                                        @case(5)
                                            <span title="Très satisfait" data-bs-toggle="tooltip">😀</span>
                                            @break
                                        @default
                                            <span>Non évalué</span>
                                    @endswitch
                                </div>
                                <div>
                                    @switch($feedback->satisfaction_rating)
                                        @case(1)
                                            <strong class="text-danger">Très insatisfait</strong>
                                            @break
                                        @case(2)
                                            <strong class="text-warning">Insatisfait</strong>
                                            @break
                                        @case(3)
                                            <strong>Neutre</strong>
                                            @break
                                        @case(4)
                                            <strong class="text-success">Satisfait</strong>
                                            @break
                                        @case(5)
                                            <strong class="text-success">Très satisfait</strong>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-4">
                        <h6 class="fw-semibold">Message :</h6>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($feedback->message)) !!}
                        </div>
                    </div>
                    
                    @if($feedback->photo)
                    <div class="mb-4">
                        <h6 class="fw-semibold">Photo jointe :</h6>
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $feedback->photo) }}" target="_blank">
                                <img src="{{ asset('storage/' . $feedback->photo) }}" alt="Photo jointe" class="img-fluid rounded" style="max-height: 300px">
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('feedbacks.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                        </a>
                        
                        <div>
                            <form action="{{ route('feedbacks.toggle-priority', $feedback) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn {{ $feedback->is_priority ? 'btn-outline-danger' : 'btn-danger' }}">
                                    <i class="bx bx-flag me-1"></i> {{ $feedback->is_priority ? 'Retirer priorité' : 'Marquer prioritaire' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('feedbacks.destroy', $feedback) }}" method="POST" class="d-inline ms-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette suggestion ?');">
                                    <i class="bx bx-trash me-1"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="small text-muted">Date de soumission</div>
                        <div>{{ $feedback->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted">Nom complet</div>
                        <div>{{ $feedback->nom_complet }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted">Téléphone</div>
                        <div>{{ $feedback->telephone }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted">Email</div>
                        <div>{{ $feedback->email }}</div>
                    </div>
                    
                    @if($feedback->salon)
                    <div class="mb-3">
                        <div class="small text-muted">Salon concerné</div>
                        <div>{{ $feedback->salon->nom }}</div>
                    </div>
                    @endif
                    
                    @if($feedback->numero_ticket)
                    <div class="mb-3">
                        <div class="small text-muted">Numéro de ticket</div>
                        <div>{{ $feedback->numero_ticket }}</div>
                    </div>
                    @endif
                    
                    @if($feedback->prestation)
                    <div class="mb-3">
                        <div class="small text-muted">Prestation concernée</div>
                        <div>{{ $feedback->prestation }}</div>
                    </div>
                    @endif
                    
                    @if($feedback->employee)
                    <div class="mb-3">
                        <div class="small text-muted">Employé concerné</div>
                        <div class="d-flex align-items-center">
                            @if($feedback->employee->photo)
                            <div class="me-2">
                                <img src="{{ asset('storage/' . $feedback->employee->photo) }}" 
                                     class="rounded-circle" 
                                     width="50" 
                                     height="50" 
                                     alt="Photo de {{ $feedback->employee->nom_complet }}">
                            </div>
                            @else
                            <div class="me-2">
                                <div class="avatar-placeholder rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <span class="text-white font-weight-bold">
                                        {{ substr($feedback->employee->prenom, 0, 1) }}{{ substr($feedback->employee->nom, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            @endif
                            <div>{{ $feedback->employee->nom_complet }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $feedback->email }}" class="btn btn-outline-primary">
                            <i class="bx bx-envelope me-1"></i> Répondre par email
                        </a>
                        
                        @if($feedback->telephone)
                        <a href="tel:{{ $feedback->telephone }}" class="btn btn-outline-info">
                            <i class="bx bx-phone me-1"></i> Appeler
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
