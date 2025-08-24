@extends('layouts.app')

@section('title', 'Suggestions et préoccupations')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion /</span> Suggestions et préoccupations
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des suggestions et préoccupations</h5>
            @can('create feedbacks')
            <a href="{{ route('feedbacks.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Nouvelle suggestion
            </a>
            @endcan
        </div>

        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form action="{{ route('feedbacks.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Rechercher un feedback par nom, sujet ou message..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">Rechercher</button>
                        @if(request()->has('search'))
                            <a href="{{ route('feedbacks.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
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

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Priorité</th>
                            <th>Client</th>
                            <th>Salon</th>
                            <th>Prestation</th>
                            <th>Satisfaction</th>
                            <th>Sujet</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($feedbacks as $feedback)
                            <tr class="{{ !$feedback->is_read ? 'table-active' : '' }} {{ $feedback->is_priority ? 'table-warning' : '' }}">
                                <td>{{ $feedback->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($feedback->is_priority)
                                        <span class="badge bg-danger">Prioritaire</span>
                                    @else
                                        <span class="badge bg-secondary">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $feedback->nom_complet }}</strong><br>
                                    <small>{{ $feedback->email }}</small>
                                    @if($feedback->telephone)
                                        <br><small><i class="bx bx-phone"></i> {{ $feedback->telephone }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($feedback->salon)
                                        {{ $feedback->salon->nom }}
                                    @else
                                        <span class="text-muted">Non spécifié</span>
                                    @endif
                                </td>
                                <td>
                                    @if($feedback->prestation)
                                        {{ $feedback->prestation }}
                                    @else
                                        <span class="text-muted">Non spécifié</span>
                                    @endif
                                </td>
                                <td>
                                    @if($feedback->satisfaction_rating)
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
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
                                                @endswitch
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Non évalué</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($feedback->sujet, 50) }}</td>
                                <td>
                                    @if ($feedback->is_read)
                                        <span class="badge bg-success">Lu</span>
                                    @else
                                        <span class="badge bg-info">Non lu</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @can('view feedbacks')
                                            <a class="dropdown-item" href="{{ route('feedbacks.show', $feedback) }}">
                                                <i class="bx bx-show-alt me-1"></i> Voir
                                            </a>
                                            @endcan
                                            @can('edit feedbacks')
                                            @if (!$feedback->is_read)
                                                <form action="{{ route('feedbacks.mark-read', $feedback) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bx bx-check-double me-1"></i> Marquer comme lu
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('feedbacks.toggle-priority', $feedback) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bx bx-flag me-1"></i> 
                                                    {{ $feedback->is_priority ? 'Retirer priorité' : 'Marquer prioritaire' }}
                                                </button>
                                            </form>
                                            @endcan
                                            @can('delete feedbacks')
                                            <form action="{{ route('feedbacks.destroy', $feedback) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette suggestion ?');">
                                                    <i class="bx bx-trash me-1"></i> Supprimer
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">
                                    <div class="text-muted">
                                        <i class="bx bx-message-square-x fs-4 mb-2"></i>
                                        <p>Aucune suggestion ou préoccupation trouvée</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 px-2">
                {{ $feedbacks->links('pagination::bootstrap-5') }}
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
