@extends('layouts.app')

@section('title', 'Créer un Salon')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Gestion / <a href="{{ route('salons.index') }}">Salons</a> /</span> Créer
</h4>

<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Nouveau Salon</h5>
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
                
                <form action="{{ route('salons.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nom">Nom du Salon</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" placeholder="Nom du salon" required />
                        </div>
                    </div>
                    
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <a href="{{ route('salons.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
