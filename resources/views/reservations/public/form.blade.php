<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Réservation en ligne | SPA</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        :root {
            --primary-color: #81b29a;
            --primary-light: #a7cdb8;
            --primary-dark: #5f9380;
            --accent-color: #f2cc8f;
            --text-dark: #3d405b;
            --text-light: #6e6b7b;
            --white: #ffffff;
            --light-bg: #f8f9fa;
            --border-radius: 12px;
            --box-shadow: 0 10px 25px rgba(129, 178, 154, 0.15);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            line-height: 1.7;
        }
        
        .reservation-container {
            max-width: 900px;
            margin: 40px auto;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .reservation-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .reservation-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 60%);
            transform: rotate(30deg);
            z-index: 1;
        }
        
        .reservation-header h2 {
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        
        .reservation-header p {
            opacity: 0.9;
            font-weight: 300;
            font-size: 1.1rem;
            position: relative;
            z-index: 2;
        }
        
        .reservation-body {
            padding: 40px;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: rgba(248, 249, 250, 0.5);
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
            transition: var(--transition);
        }
        
        .form-section:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        
        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--primary-dark);
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .form-control, .form-select {
            border: 1px solid #e2e2e2;
            padding: 12px;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(115, 103, 240, 0.25);
        }
        
        .input-group-text {
            background-color: var(--primary-light);
            color: var(--white);
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 8px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(115, 103, 240, 0.4);
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(115, 103, 240, 0.5);
        }
        
        .alert-info {
            background-color: rgba(115, 103, 240, 0.1);
            border-left: 4px solid var(--primary-color);
            border-top: none;
            border-right: none;
            border-bottom: none;
            color: var(--text-dark);
            border-radius: 8px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            color: var(--text-light);
            background-color: var(--light-bg);
            border-top: 1px solid #e9ecef;
            margin-top: 30px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .reservation-container {
                margin: 20px auto;
            }
            
            .reservation-body {
                padding: 20px;
            }
            
            .form-section {
                padding: 15px;
            }
        }
        
        /* Bannière et éléments spa */
        .spa-banner {
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-top: 30px;
            max-height: 250px;
        }
        
        .spa-banner-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 3s ease;
            filter: brightness(0.85);
        }
        
        .spa-banner:hover .spa-banner-img {
            transform: scale(1.05);
        }
        
        .spa-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.85);
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            letter-spacing: 1px;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
        }
        
        .spa-icon-container {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }
        
        .spa-icon {
            position: absolute;
            font-size: 24px;
            color: var(--accent-color);
            opacity: 0.7;
        }
        
        .icon-1 { top: 15%; left: 10%; animation: float 6s ease-in-out infinite; }
        .icon-2 { top: 25%; right: 15%; animation: float 8s ease-in-out infinite 1s; }
        .icon-3 { bottom: 20%; left: 15%; animation: float 7s ease-in-out infinite 2s; }
        .icon-4 { bottom: 30%; right: 10%; animation: float 9s ease-in-out infinite 3s; }
        
        /* Styles pour la galerie d'images services */
        .service-card {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 180px;
            cursor: pointer;
        }
        
        .service-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .service-card:hover img {
            transform: scale(1.1);
        }
        
        .service-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            padding: 15px;
            color: white;
            text-align: center;
        }
        
        .service-overlay h5 {
            margin: 0;
            font-weight: 500;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        
        @keyframes float {
            0% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-15px) rotate(5deg); }
            100% { transform: translateY(0) rotate(0); }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated {
            animation: fadeIn 0.8s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="container animated">
        <!-- Bannière décorative supérieure -->
        <div class="spa-banner mb-4 text-center">
            <img src="https://images.unsplash.com/photo-1600334129128-685c5582fd35?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Spa Ambiance" class="img-fluid rounded spa-banner-img">
            <div class="spa-logo">
                <i class="fas fa-spa"></i> SPA & Bien-Être
            </div>
            <!-- Icônes décoratives flottantes -->
            <div class="spa-icon-container">
                <i class="fas fa-hot-tub spa-icon icon-1"></i>
                <i class="fas fa-leaf spa-icon icon-2"></i>
                <i class="fas fa-hand-sparkles spa-icon icon-3"></i>
                <i class="fas fa-cloud spa-icon icon-4"></i>
            </div>
        </div>
        
        <!-- Galerie d'images de services SPA -->
        <div class="row mb-4 animated">
            <div class="col-md-4 mb-3">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" class="img-fluid rounded" alt="Massage">
                    <div class="service-overlay">
                        <h5>Massage relaxant</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" class="img-fluid rounded" alt="Soins visage">
                    <div class="service-overlay">
                        <h5>Soins du visage</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1519823551278-64ac92734fb1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" class="img-fluid rounded" alt="Manucure">
                    <div class="service-overlay">
                        <h5>Manucure & pédicure</h5>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="reservation-container">
            <div class="reservation-header">
                <h2 class="mb-0"><i class="fas fa-calendar-check"></i> Réservation en ligne</h2>
                <p class="mb-0">Prenez rendez-vous facilement en quelques clics</p>
            </div>

            <div class="reservation-body">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible mb-4">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('reservations.public.store') }}" method="POST">
                    @csrf
                    
                    <!-- Section Informations personnelles -->
                    <div class="form-section animated">
                        <h4 class="section-title"><i class="fas fa-user"></i> Informations personnelles</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom_complet" class="form-label">Nom complet</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                    <input type="text" class="form-control" id="nom_complet" name="nom_complet" placeholder="Votre nom et prénom" value="{{ old('nom_complet') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numero_telephone" class="form-label">Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" id="numero_telephone" name="numero_telephone" placeholder="Votre numéro de téléphone" value="{{ old('numero_telephone') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adresse_mail" class="form-label">Adresse e-mail</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="adresse_mail" name="adresse_mail" placeholder="Votre adresse e-mail" value="{{ old('adresse_mail') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Détails de la prestation -->
                    <div class="form-section animated">
                        <h4 class="section-title"><i class="fas fa-spa"></i> Détails de votre prestation</h4>
                        
                        <!-- La sélection du salon a été retirée - Le salon sera attribué par l'administrateur -->
                        <input type="hidden" name="salon_id" value="1" /> <!-- Valeur temporaire qui sera remplacée par l'administrateur -->

                        <div class="mb-4">
                            <label class="form-label mb-3">Choisissez vos prestations</label>
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i> Sélectionnez une ou plusieurs prestations. Le prix total et la durée totale seront automatiquement calculés.
                            </div>
                            <div class="table-responsive prestations-table">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 70px;" class="text-center">Sélection</th>
                                            <th>Prestation</th>
                                            <th style="width: 140px;" class="text-end">Prix (FCFA)</th>
                                            <th style="width: 100px;" class="text-center">Durée</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($prestations as $prestation)
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input prestation-checkbox" type="checkbox" 
                                                        value="{{ $prestation->id }}" id="prestation_{{ $prestation->id }}" 
                                                        name="prestations[]" data-prix="{{ $prestation->prix }}" 
                                                        data-duree="{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}"
                                                        {{ (is_array(old('prestations')) && in_array($prestation->id, old('prestations'))) ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>{{ $prestation->nom_prestation }}</td>
                                            <td class="text-end">{{ number_format($prestation->prix, 0, ',', ' ') }}</td>
                                            <td class="text-center">{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Horaire -->
                    <div class="form-section animated">
                        <h4 class="section-title"><i class="fas fa-clock"></i> Date et heure du rendez-vous</h4>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date_heure" class="form-label">Date et heure souhaitée</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="datetime-local" class="form-control" id="date_heure" name="date_heure" value="{{ old('date_heure') }}" required>
                                </div>
                                <div class="form-text">Choisissez une date et une heure disponibles</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="prestation_info" class="form-label">Durée estimée</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hourglass-half"></i></span>
                                    <input type="text" class="form-control" id="prestation_info" readonly placeholder="Sélectionnez une prestation">
                                </div>
                                <div class="form-text">Durée totale des prestations</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="prix_total" class="form-label">Prix total</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="text" class="form-control" id="prix_total" readonly placeholder="Sélectionnez une prestation">
                                </div>
                                <div class="form-text">Coût total des prestations</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Commentaires -->
                    <div class="form-section animated">
                        <h4 class="section-title"><i class="fas fa-comment-alt"></i> Informations additionnelles</h4>
                        
                        <div class="mb-4">
                            <label for="commentaire" class="form-label">Commentaires (facultatif)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="3" placeholder="Informations supplémentaires pour votre réservation...">{{ old('commentaire') }}</textarea>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Votre réservation sera en attente de confirmation par notre équipe.
                            Nous vous contacterons par téléphone ou par email pour confirmer votre rendez-vous.
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-calendar-check me-2"></i> Réserver maintenant</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="footer">
            <p><i class="fas fa-heart text-danger me-1"></i> &copy; {{ date('Y') }} SPA - Tous droits réservés</p>
        </div>
    </div>

    <!-- Bootstrap JS & Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Récupération des détails des prestations via checkboxes
            const checkboxes = document.querySelectorAll('.prestation-checkbox');
            const prestationInfoField = document.getElementById('prestation_info');
            const prixTotalField = document.getElementById('prix_total');
            
            // Fonction pour calculer les totaux
            function calculerTotaux() {
                const selectedCheckboxes = document.querySelectorAll('.prestation-checkbox:checked');
                
                if (selectedCheckboxes.length === 0) {
                    prestationInfoField.value = 'Sélectionnez au moins une prestation';
                    prixTotalField.value = 'Sélectionnez au moins une prestation';
                    return;
                }
                
                // Récupérer les IDs des prestations sélectionnées
                const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
                
                // Utiliser FormData pour envoyer un tableau de valeurs
                const formData = new FormData();
                selectedIds.forEach(id => {
                    formData.append('prestations[]', id);
                });
                
                fetch('{{ route("reservations.public.getPrestationDetails") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Format de la durée totale
                        prestationInfoField.value = `${data.duree}`;
                        
                        // Affichage du prix total
                        if (prixTotalField) {
                            prixTotalField.value = `${data.prix.toLocaleString('fr-FR')} FCFA`;
                        }
                    } else {
                        prestationInfoField.value = 'Information non disponible';
                        prixTotalField.value = 'Information non disponible';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    prestationInfoField.value = 'Erreur lors de la récupération des informations';
                    prixTotalField.value = 'Erreur lors de la récupération des informations';
                });
            }
            

            // Ajouter les écouteurs d'événements aux cases à cocher
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', calculerTotaux);
            });
            
            // Vérifier s'il y a des prestations déjà sélectionnées au chargement
            const selectedInitial = document.querySelectorAll('.prestation-checkbox:checked');
            if (selectedInitial.length > 0) {
                calculerTotaux();
            }
            
            // Ajouter un style personnalisé au tableau des prestations
            const prestationsTable = document.querySelector('.prestations-table');
            if (prestationsTable) {
                prestationsTable.style.borderRadius = '8px';
                prestationsTable.style.overflow = 'hidden';
            }
        });
    </script>
</body>
</html>
