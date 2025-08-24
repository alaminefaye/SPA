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
        
        .header-image {
            max-height: 200px;
            object-fit: contain;
        }
        .card-form {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-bottom: none;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        .employee-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .employee-item:hover {
            background-color: #f8f9fa;
        }
        .employee-item.active {
            background-color: #e7f1ff;
            border-color: #4e73df;
            font-weight: bold;
        }
        .employee-photo-container {
            width: 100%;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }
        .employee-photo-container img {
            max-height: 150px;
            max-width: 100%;
            object-fit: cover;
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
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
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
                            <div class="mb-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" class="form-control" id="searchPrestation" placeholder="Rechercher une prestation...">
                                </div>
                                <div class="form-text">Commencez à taper pour voir les prestations disponibles</div>
                            </div>
                            <div class="table-responsive prestations-table" style="max-height: 200px; overflow-y: auto;">
                                <table class="table table-bordered table-hover">
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
                                        <tr class="prestation-row" style="display: none;">
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input prestation-radio" type="radio" 
                                                        value="{{ $prestation->nom_prestation }}" id="prestation_{{ $prestation->id }}" 
                                                        name="prestation" data-prix="{{ $prestation->prix }}" 
                                                        data-duree="{{ $prestation->duree ? $prestation->duree->format('H:i') : '00:00' }}"
                                                        {{ old('prestation') == $prestation->nom_prestation ? 'checked' : '' }}>
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
                            @error('prestation')
                                <div class="text-danger mt-2">{{ $message }}</div>
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
                            <label class="form-label">Employé concerné</label>
                            <div class="row employee-selection-container">
                                <div class="col-md-8">
                                    <ul class="list-group">
                                        @foreach($employees as $employee)
                                            <li class="list-group-item employee-item" data-employee-id="{{ $employee->id }}" data-photo="{{ $employee->photo ? asset('storage/'.$employee->photo) : asset('assets/img/default-profile.png') }}">
                                                {{ $employee->nom_complet }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <div class="employee-photo-container">
                                        <img id="selected-employee-photo" src="{{ asset('assets/img/default-profile.png') }}" alt="Photo de l'employé" class="img-fluid rounded">
                                    </div>
                                </div>
                                <input type="hidden" name="employee_id" id="employee_id" value="{{ old('employee_id') }}">
                            </div>
                            @error('employee_id')
                                <div class="text-danger">{{ $message }}</div>
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
    <!-- jQuery (needed for employee selection) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Gestion du champ de recherche des prestations
            const searchInput = document.getElementById('searchPrestation');
            
            // Fonction pour retirer les accents d'une chaîne
            function removeAccents(str) {
                return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            }

            // Fonction pour filtrer les prestations basée sur la recherche
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const searchTermWithoutAccent = removeAccents(searchTerm);
                
                document.querySelectorAll('.prestation-row').forEach(row => {
                    const prestationName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const prestationNameWithoutAccent = removeAccents(prestationName);
                    
                    if (searchTerm === '') {
                        // Si le champ de recherche est vide, ne rien afficher
                        row.style.display = 'none';
                    } else if (prestationName.includes(searchTerm) || prestationNameWithoutAccent.includes(searchTermWithoutAccent)) {
                        // Afficher les lignes qui correspondent à la recherche (avec ou sans accent)
                        row.style.display = '';
                    } else {
                        // Cacher les lignes qui ne correspondent pas à la recherche
                        row.style.display = 'none';
                    }
                });
            });
            
            // Si une prestation était précédemment sélectionnée (validation error), l'afficher
            const savedPrestation = $('input[name="prestation"]:checked').val();
            if (savedPrestation) {
                searchInput.value = savedPrestation;
                searchInput.dispatchEvent(new Event('input'));
            }
            
            // Salon change handler
            $('#salon_id').change(function() {
                const salonId = $(this).val();
                if (!salonId) {
                    // Réinitialiser la recherche de prestation
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                }
            });

            // Gestion de la sélection des employés
            $('.employee-item').click(function() {
                $('.employee-item').removeClass('active');
                $(this).addClass('active');
                
                const employeeId = $(this).data('employee-id');
                const photoUrl = $(this).data('photo');
                
                $('#employee_id').val(employeeId);
                $('#selected-employee-photo').attr('src', photoUrl);
            });

            // Si un employé était précédemment sélectionné (validation error), le resélectionner
            const savedEmployeeId = $('#employee_id').val();
            if (savedEmployeeId) {
                $(`.employee-item[data-employee-id="${savedEmployeeId}"]`).click();
            }
            
            // Ajouter un style personnalisé au tableau des prestations
            const prestationsTable = document.querySelector('.prestations-table');
            if (prestationsTable) {
                prestationsTable.style.borderRadius = '8px';
                prestationsTable.style.overflow = 'hidden';
            }
        });
        
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
