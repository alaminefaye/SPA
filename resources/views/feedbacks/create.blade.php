@extends('layouts.app')

@section('title', 'Nouvelle suggestion')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gestion / Suggestions /</span> Nouvelle
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Nouvelle suggestion ou préoccupation</h5>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('feedbacks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="from_admin" value="1">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nom_complet" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nom_complet') is-invalid @enderror" id="nom_complet" name="nom_complet" value="{{ old('nom_complet') }}" required>
                                @error('nom_complet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="telephone" class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" required>
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="salon_id" class="form-label">Salon concerné</label>
                                <select class="form-select @error('salon_id') is-invalid @enderror" id="salon_id" name="salon_id">
                                    <option value="">-- Sélectionnez un salon --</option>
                                    @foreach ($salons as $salon)
                                        <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>{{ $salon->name }}</option>
                                    @endforeach
                                </select>
                                @error('salon_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="numero_ticket" class="form-label">Numéro de ticket (si applicable)</label>
                                <input type="text" class="form-control @error('numero_ticket') is-invalid @enderror" id="numero_ticket" name="numero_ticket" value="{{ old('numero_ticket') }}">
                                @error('numero_ticket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="prestation" class="form-label">Prestation concernée</label>
                            <input type="text" class="form-control @error('prestation') is-invalid @enderror" id="prestation" name="prestation" value="{{ old('prestation') }}">
                            @error('prestation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="sujet" class="form-label">Sujet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sujet') is-invalid @enderror" id="sujet" name="sujet" value="{{ old('sujet') }}" required>
                            @error('sujet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="photo" class="form-label">Photo (si applicable, max 10 MB)</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                            <div class="form-text">Formats acceptés : JPEG, PNG, JPG, GIF - Maximum 10 MB</div>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_priority" name="is_priority" value="1" {{ old('is_priority') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_priority">Marquer comme prioritaire</label>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-between">
                                <a href="{{ route('feedbacks.index') }}" class="btn btn-secondary">
                                    <i class="bx bx-arrow-back me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
