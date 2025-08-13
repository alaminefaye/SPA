@extends('layouts.app')

@section('title', 'Détails du journal d\'activité')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Détails du journal d'activité #{{ $log->id }}</h5>
                <a href="{{ route('activity.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 25%">ID</th>
                                <td>{{ $log->id }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $log->description }}</td>
                            </tr>
                            <tr>
                                <th>Événement</th>
                                <td>
                                    <span class="badge bg-{{ $log->event == 'created' ? 'success' : ($log->event == 'updated' ? 'info' : ($log->event == 'deleted' ? 'danger' : 'secondary')) }}">
                                        {{ $log->event }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Sujet</th>
                                <td>
                                    {{ $log->subject_type ?? 'N/A' }}
                                    @if($log->subject_id)
                                        #{{ $log->subject_id }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Utilisateur</th>
                                <td>
                                    @if($log->causer)
                                        {{ $log->causer->name ?? 'ID: ' . $log->causer_id }}
                                    @else
                                        Système
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Date/Heure</th>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Propriétés</th>
                                <td>
                                    @if($log->properties && $log->properties->count() > 0)
                                        <div class="accordion" id="propertiesAccordion">
                                            @if($log->properties->has('attributes'))
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAttributes" aria-expanded="true" aria-controls="collapseAttributes">
                                                            Attributs
                                                        </button>
                                                    </h2>
                                                    <div id="collapseAttributes" class="accordion-collapse collapse show" data-bs-parent="#propertiesAccordion">
                                                        <div class="accordion-body">
                                                            <pre class="mb-0"><code>{{ json_encode($log->properties->get('attributes'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($log->properties->has('old'))
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOld" aria-expanded="false" aria-controls="collapseOld">
                                                            Anciennes valeurs
                                                        </button>
                                                    </h2>
                                                    <div id="collapseOld" class="accordion-collapse collapse" data-bs-parent="#propertiesAccordion">
                                                        <div class="accordion-body">
                                                            <pre class="mb-0"><code>{{ json_encode($log->properties->get('old'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Aucune propriété enregistrée</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 d-flex justify-content-between">
                    <form action="{{ route('activity.destroy', $log->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce journal?')">
                            <i class="bx bx-trash me-1"></i> Supprimer ce journal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
