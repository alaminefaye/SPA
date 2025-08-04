@extends('layouts.app')

@section('title', 'Modifier un Client')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('clients.index') }}">Clients</a> /</span> Modifier
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Modifier le Client</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('clients.update', $client->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nom_complet">Nom Complet</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nom_complet" name="nom_complet" value="{{ old('nom_complet', $client->nom_complet) }}" placeholder="Nom complet du client" required />
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="numero_telephone">Numéro Téléphone</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="numero_telephone" name="numero_telephone" value="{{ old('numero_telephone', $client->numero_telephone) }}" placeholder="Numéro de téléphone" required />
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="adresse_mail">Adresse Mail</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="adresse_mail" name="adresse_mail" value="{{ old('adresse_mail', $client->adresse_mail) }}" placeholder="Adresse email" required />
                        </div>
                    </div>
                    
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
