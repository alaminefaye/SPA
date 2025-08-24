@extends('layouts.app')

@section('title', 'Anniversaires à venir')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('clients.index') }}">Clients</a> /</span> Anniversaires à venir
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Anniversaires des clients</h5>
    </div>
    
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-8">
                <form action="{{ route('clients.anniversaires') }}" method="GET" class="d-flex">
                    <div class="input-group me-2">
                        <input type="text" class="form-control" placeholder="Rechercher par nom ou téléphone" name="search" value="{{ $search ?? '' }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
                    
                    <div class="input-group">
                        <select class="form-select" name="periode" onchange="this.form.submit()">
                            <option value="aujourdhui" {{ ($periode ?? '') == 'aujourdhui' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="semaine" {{ ($periode ?? '') == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="mois" {{ ($periode ?? '') == 'mois' ? 'selected' : '' }}>Ce mois-ci</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(count($clientsAnniversaires) > 0)
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom Complet</th>
                            <th>Téléphone</th>
                            <th>Date de Naissance</th>
                            <th>Anniversaire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientsAnniversaires as $client)
                            <tr class="{{ $client->isAnniversaireToday() ? 'table-success' : '' }}">
                                <td>{{ $client->nom_complet }}</td>
                                <td>{{ $client->numero_telephone }}</td>
                                <td>{{ $client->getDateAnniversaireFormattee() }}</td>
                                <td>
                                    @if($client->isAnniversaireToday())
                                        <span class="badge bg-success">Aujourd'hui!</span>
                                    @else
                                        <span class="badge bg-info">Dans {{ $client->joursAvantAnniversaire() }} jours</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-1">
                                    @if($client->isAnniversaireToday())
                                        <a href="{{ $client->getWhatsAppUrl() }}" target="_blank" class="btn btn-sm btn-success d-inline-flex align-items-center" title="Envoyer voeux d'anniversaire par WhatsApp">
                                            <i class="bx bxl-whatsapp"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i> Voir
                                    </a>
                                    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-pencil"></i> Modifier
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                Aucun anniversaire à venir pour la période sélectionnée.
            </div>
        @endif
    </div>
</div>
@endsection
