@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="section-header">
    <h1>Rapports</h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-large-icons">
                <div class="card-icon bg-primary text-white">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="card-body">
                    <h4>Rapport des prestations</h4>
                    <p>Générez des rapports détaillés sur les prestations effectuées avec statistiques et analyses.</p>
                    <a href="{{ route('reports.prestations') }}" class="card-cta">Accéder <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-large-icons">
                <div class="card-icon bg-success text-white">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="card-body">
                    <h4>Rapport des ventes de produits</h4>
                    <p>Analysez les ventes de produits avec statistiques sur les quantités vendues et les revenus générés.</p>
                    <a href="{{ route('reports.products') }}" class="card-cta">Accéder <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>À propos des rapports</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><strong>Informations importantes sur les rapports :</strong></p>
                        <ul>
                            <li>Les rapports sont générés dynamiquement selon les critères que vous choisissez.</li>
                            <li>Vous pouvez exporter les données en format Excel ou PDF.</li>
                            <li>Les rapports quotidiens concernent uniquement la journée en cours par défaut.</li>
                            <li>Pour les rapports sur mesure, spécifiez les dates de début et de fin.</li>
                            <li>Les statistiques sont calculées automatiquement.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
