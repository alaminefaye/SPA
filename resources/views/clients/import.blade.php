@extends('layouts.app')

@section('title', 'Import Clients')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Import Clients</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Importer une liste de clients</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('clients.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Fichier Excel ou CSV</label>
                            <input type="file" class="form-control-file @error('file') is-invalid @enderror" id="file" name="file" required>
                            @error('file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">Formats acceptés : xlsx, xls, csv (Max: 2Mo)</small>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-import"></i> Importer
                            </button>
                            <a href="{{ route('clients.download-template') }}" class="btn btn-info">
                                <i class="fas fa-download"></i> Télécharger le modèle
                            </a>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instructions</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Format du fichier Excel</h5>
                        <p>Le fichier doit contenir les colonnes suivantes :</p>
                        <ul>
                            <li><strong>nom_complet</strong> (Obligatoire)</li>
                            <li><strong>numero_telephone</strong> (Obligatoire)</li>
                            <li><strong>adresse_mail</strong> (Optionnel)</li>
                            <li><strong>date_naissance</strong> (Optionnel)</li>
                            <li><strong>points</strong> (Optionnel)</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h5>Conseils</h5>
                        <ul>
                            <li>Assurez-vous que les adresses email sont uniques</li>
                            <li>Respectez le format date pour la date de naissance</li>
                            <li>Téléchargez le modèle pour un exemple de format</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
