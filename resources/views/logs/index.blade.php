@extends('layouts.app')

@section('title', 'Journaux d\'activité')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Journaux d'activité</h5>
        <div>
            <form action="{{ route('activity.clearAll') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir effacer tous les journaux d\'activité?')">
                    <i class="bx bx-trash me-1"></i> Effacer tout
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Sujet</th>
                        <th>Événement</th>
                        <th>Utilisateur</th>
                        <th>Date/Heure</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->description }}</td>
                            <td>
                                {{ $log->subject_type ?? 'N/A' }}
                                @if($log->subject_id)
                                    #{{ $log->subject_id }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->event == 'created' ? 'success' : ($log->event == 'updated' ? 'info' : ($log->event == 'deleted' ? 'danger' : 'secondary')) }}">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td>
                                @if($log->causer)
                                    {{ $log->causer->name ?? 'ID: ' . $log->causer_id }}
                                @else
                                    Système
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('activity.show', $log->id) }}">
                                            <i class="bx bx-show-alt me-1"></i> Détails
                                        </a>
                                        <form action="{{ route('activity.destroy', $log->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce journal?')">
                                                <i class="bx bx-trash me-1"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucun journal d'activité trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
