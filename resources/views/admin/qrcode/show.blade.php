@extends('layouts.app')

@section('title', 'QR Code Généré')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion d'activité / <a href="{{ route('qrcode.index') }}">QR Code</a> /</span> Résultat
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">QR Code généré: {{ $name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center">
                                <h6>Aperçu du QR Code</h6>
                                <div class="qr-code-container p-3 mb-3">
                                    <img src="{{ asset('storage/' . $qrCodePath) }}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                                </div>
                                <div class="mb-3">
                                    <a href="{{ route('qrcode.download', basename($qrCodePath)) }}" class="btn btn-primary">
                                        <i class='bx bx-download me-1'></i> Télécharger le QR Code
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Informations</h6>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Nom</th>
                                                <td>{{ $name }}</td>
                                            </tr>
                                            <tr>
                                                <th>URL</th>
                                                <td><a href="{{ $link }}" target="_blank">{{ $link }}</a></td>
                                            </tr>
                                            <tr>
                                                <th>Date de création</th>
                                                <td>{{ now()->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6>Comment utiliser ce QR Code</h6>
                                <ul class="ps-3">
                                    <li>Téléchargez le QR code en cliquant sur le bouton ci-contre</li>
                                    <li>Imprimez-le et placez-le dans un endroit visible</li>
                                    <li>Les utilisateurs pourront le scanner avec leur téléphone pour accéder directement à l'URL configurée</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <a href="{{ route('qrcode.index') }}" class="btn btn-secondary">
                                <i class='bx bx-plus me-1'></i> Créer un nouveau QR Code
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
