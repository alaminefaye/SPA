@extends('layouts.app')

@section('title', 'Ajouter un employé')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Ajouter un employé</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employés</a></li>
        <li class="breadcrumb-item active">Ajouter</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus me-1"></i>
            Informations de l'employé
        </div>
        <div class="card-body">
            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="numero_telephone" class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('numero_telephone') is-invalid @enderror" id="numero_telephone" name="numero_telephone" value="{{ old('numero_telephone') }}" required>
                            @error('numero_telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}">
                            @error('date_naissance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_embauche" class="form-label">Date d'embauche <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_embauche') is-invalid @enderror" id="date_embauche" name="date_embauche" value="{{ old('date_embauche', date('Y-m-d')) }}" required>
                            @error('date_embauche')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="poste" class="form-label">Poste <span class="text-danger">*</span></label>
                            <select class="form-select @error('poste') is-invalid @enderror" id="poste" name="poste" required>
                                <option value="" selected disabled>Sélectionner un poste</option>
                                @foreach($postes as $key => $value)
                                    <option value="{{ $key }}" {{ old('poste') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('poste')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="salon_id" class="form-label">Salon</label>
                            <select class="form-select @error('salon_id') is-invalid @enderror" id="salon_id" name="salon_id">
                                <option value="">Non assigné</option>
                                @foreach($salons as $id => $nom)
                                    <option value="{{ $id }}" {{ old('salon_id') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                            @error('salon_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="specialites" class="form-label">Spécialités</label>
                            <textarea class="form-control @error('specialites') is-invalid @enderror" id="specialites" name="specialites" rows="3">{{ old('specialites') }}</textarea>
                            <small class="form-text text-muted">Entrez les spécialités séparées par des virgules (ex: massage, soins visage, manucure)</small>
                            @error('specialites')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="salaire" class="form-label">Salaire</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('salaire') is-invalid @enderror" id="salaire" name="salaire" value="{{ old('salaire') }}" step="0.01" min="0">
                                <span class="input-group-text">FCFA</span>
                            </div>
                            @error('salaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="2">{{ old('adresse') }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo">
                            <small class="form-text text-muted">Format accepté: JPG, PNG, GIF (max: 2MB)</small>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input @error('actif') is-invalid @enderror" type="checkbox" id="actif" name="actif" value="1" {{ old('actif', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="actif">
                        Employé actif
                    </label>
                    @error('actif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
