@extends('layouts.app')

@section('title', 'Séance Créée')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Séance créée avec succès</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="bx bx-check-circle me-2"></i>
                    La séance a été créée avec succès. Le ticket d'impression s'ouvrira dans une nouvelle fenêtre.
                </div>
                
                <p>Si le ticket ne s'ouvre pas automatiquement, cliquez sur le bouton ci-dessous :</p>
                <a href="{{ $ticketUrl }}" target="_blank" class="btn btn-primary">
                    <i class="bx bx-printer me-1"></i> Ouvrir le ticket d'impression
                </a>
                
                <div class="mt-4">
                    <a href="{{ $redirectUrl }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Retour à la liste des séances
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ouvrir automatiquement le ticket dans une nouvelle fenêtre
        window.open('{{ $ticketUrl }}', '_blank');
    });
</script>
@endsection
