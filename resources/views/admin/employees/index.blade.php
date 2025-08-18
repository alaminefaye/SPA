@extends('layouts.app')

@section('title', 'Liste des employés')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Gestion des employés</h1>
        @can('create employees')
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvel employé
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Liste des employés
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="employeesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Poste</th>
                            <th>Téléphone</th>
                            <th>Salon</th>
                            <th>Date d'embauche</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td class="text-center">
                                @if($employee->photo)
                                <img src="{{ Storage::url($employee->photo) }}" alt="Photo de {{ $employee->nom_complet }}" 
                                    class="rounded-circle employee-thumbnail" width="40" height="40">
                                @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center employee-thumbnail" 
                                    style="width: 40px; height: 40px; color: white;">
                                    <i class="fas fa-user"></i>
                                </div>
                                @endif
                            </td>
                            <td>{{ $employee->prenom }} {{ $employee->nom }}</td>
                            <td>{{ $employee->poste }}</td>
                            <td>{{ $employee->numero_telephone }}</td>
                            <td>{{ $employee->salon ? $employee->salon->nom : 'Non assigné' }}</td>
                            <td>{{ $employee->date_embauche->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $employee->actif ? 'bg-success' : 'bg-danger' }}">
                                    {{ $employee->actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    @can('view employees')
                                    <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-sm btn-info me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('edit employees')
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('toggle employee status')
                                    <form action="{{ route('admin.employees.toggle-status', $employee) }}" method="POST" class="me-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $employee->actif ? 'btn-warning' : 'btn-success' }}" 
                                                title="{{ $employee->actif ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas {{ $employee->actif ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                        </button>
                                    </form>
                                    @endcan
                                    
                                    @can('delete employees')
                                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Aucun employé trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#employeesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
            },
            "paging": false,
            "info": false,
        });
    });
</script>
@endsection
