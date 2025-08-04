@extends('layouts.app')

@section('title', 'Modifier une Prestation')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('prestations.index') }}">Prestations</a> /</span> Modifier
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Modifier la Prestation</h5>
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
                
                <form action="{{ route('prestations.update', $prestation->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nom_prestation">Nom de la Prestation</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nom_prestation" name="nom_prestation" 
                                value="{{ old('nom_prestation', $prestation->nom_prestation) }}" placeholder="Nom de la prestation" required />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="prix">Prix (FCFA)</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="prix" name="prix" 
                                value="{{ old('prix', $prestation->prix) }}" step="0.01" min="0" placeholder="0.00" required />
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="duree">Durée</label>
                        <div class="col-sm-10">
                            <input type="time" class="form-control" id="duree" name="duree" 
                                value="{{ old('duree', \Carbon\Carbon::parse($prestation->duree)->format('H:i:s')) }}" step="1" required />
                            <small class="text-muted">Format: Heures:Minutes:Secondes</small>
                        </div>
                    </div>
                    
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            <a href="{{ route('prestations.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
