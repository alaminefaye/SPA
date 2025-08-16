@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5">
                <div class="card-header bg-danger text-white">
                    <h4 class="m-0">Accès refusé</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-4x text-danger"></i>
                    </div>
                    <h2 class="mb-4">Vous n'avez pas la permission d'accéder à cette page</h2>
                    <p class="lead">Veuillez contacter l'administrateur si vous pensez que c'est une erreur.</p>
                    
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-3 mr-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
