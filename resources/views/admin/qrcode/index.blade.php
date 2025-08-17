@extends('layouts.app')

@section('title', 'Génération de QR Code')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion d'activité / </span> Génération de QR Code
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Créer un QR Code</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible mb-3">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('qrcode.generate') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="name">Nom du QR Code</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Entrez un nom pour identifier ce QR code" value="{{ old('name') }}" required>
                                <div class="form-text">Ce nom sera utilisé pour identifier ce QR code</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="link">Lien URL</label>
                            <div class="col-sm-10">
                                <input type="url" class="form-control" id="link" name="link" placeholder="https://exemple.com" value="{{ old('link') }}" required>
                                <div class="form-text">URL complète que le QR code permettra d'ouvrir</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="logo">Logo (optionnel)</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <div class="form-text">Logo à afficher au centre du QR code (fichier image JPG, PNG ou GIF)</div>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Générer QR Code</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
