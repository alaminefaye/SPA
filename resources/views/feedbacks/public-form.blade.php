<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Suggestions et préoccupations - Jared SPA</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        
        .spa-header {
            background-color: #ff69b4;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .spa-header h1 {
            font-weight: 300;
        }
        
        .feedback-form-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .feedback-form-container h2 {
            color: #ff69b4;
            margin-bottom: 25px;
            font-weight: 500;
        }
        
        .form-label {
            font-weight: 500;
        }
        
        .btn-submit {
            background-color: #ff69b4;
            border-color: #ff69b4;
            padding: 10px 25px;
            font-weight: 500;
        }
        
        .btn-submit:hover {
            background-color: #ff4da6;
            border-color: #ff4da6;
        }
        
        .spa-image-container {
            position: relative;
            height: 100%;
            min-height: 300px;
            background-image: url('https://images.unsplash.com/photo-1600334129128-685c5582fd35?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80');
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .spa-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 105, 180, 0.3);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .spa-quote {
            font-size: 24px;
            font-style: italic;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="spa-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class='bx bx-spa'></i> Jared SPA - Votre bien-être est notre priorité</h1>
                </div>
                
            </div>
        </div>
    </header>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-7">
                <div class="feedback-form-container">
                    <h2 class="text-center mb-4">Suggestions et préoccupations</h2>
                    <p class="text-muted mb-4">Nous valorisons votre opinion et souhaitons constamment améliorer nos services. N'hésitez pas à partager vos suggestions ou préoccupations avec nous.</p>
                    
                    <form action="{{ route('feedbacks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
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
                                        <option value="{{ $salon->id }}" {{ old('salon_id') == $salon->id ? 'selected' : '' }}>{{ $salon->nom }}</option>
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
                            <select class="form-select @error('prestation') is-invalid @enderror" id="prestation" name="prestation">
                                <option value="">-- Sélectionnez une prestation --</option>
                                @foreach ($prestations as $prestation)
                                    <option value="{{ $prestation->nom_prestation }}" {{ old('prestation') == $prestation->nom_prestation ? 'selected' : '' }}>
                                        {{ $prestation->nom_prestation }} ({{ number_format($prestation->prix, 0, ',', ' ') }} FCFA - {{ $prestation->duree->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
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
                            <label for="message" class="form-label">Votre message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-submit px-5">Envoyer</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="spa-image-container">
                    <div class="spa-image-overlay">
                        <p class="spa-quote">Votre confort et satisfaction sont au cœur de nos préoccupations.</p>
                        <p class="lead">Nous vous remercions de prendre le temps de partager votre expérience avec nous.</p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                
                <div class="col-md-6 text-md-end">
                    <p class="small">&copy; {{ date('Y') }} Jared SPA. Tous droits réservés.</p>
                    
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour supprimer les cartes dupliquées en bas de page
        document.addEventListener('DOMContentLoaded', function() {
            // On attend que le DOM soit complètement chargé
            setTimeout(function() {
                // Recherche tous les éléments avec les titres de nos cartes originales
                const pourquoiContacterElems = document.querySelectorAll('.card-title:not(#card-pourquoi-contacter .card-title)');
                const contactDirectElems = document.querySelectorAll('.card-title:not(#card-contact-direct .card-title)');
                
                // Pour chaque titre trouvé qui contient le texte "Pourquoi nous contacter"
                pourquoiContacterElems.forEach(function(elem) {
                    if (elem.textContent.includes('Pourquoi nous contacter')) {
                        // Trouver la carte parente et la supprimer
                        const parentCard = elem.closest('.card');
                        if (parentCard && parentCard.id !== 'card-pourquoi-contacter') {
                            parentCard.remove();
                        }
                    }
                });
                
                // Pour chaque titre trouvé qui contient le texte "Nous contacter directement"
                contactDirectElems.forEach(function(elem) {
                    if (elem.textContent.includes('Nous contacter directement')) {
                        // Trouver la carte parente et la supprimer
                        const parentCard = elem.closest('.card');
                        if (parentCard && parentCard.id !== 'card-contact-direct') {
                            parentCard.remove();
                        }
                    }
                });
            }, 100); // Petit délai pour s'assurer que le DOM est entièrement chargé
        });
    </script>
</body>
</html>
