<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation confirmée | SPA</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom styles -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .confirmation-container {
            max-width: 700px;
            margin: 80px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 40px;
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #7367f0;
            border-color: #7367f0;
        }
        .btn-primary:hover {
            background-color: #635ac7;
            border-color: #635ac7;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-container">
            <i class="fas fa-check-circle success-icon"></i>
            
            <h1 class="mb-4">Votre demande de réservation a été envoyée !</h1>
            
            <p class="lead mb-4">
                Merci d'avoir choisi nos services. Votre demande de réservation a été enregistrée avec succès.
            </p>
            
            <div class="alert alert-info mb-4">
                <p class="mb-0">Notre équipe va examiner votre demande et vous contactera très prochainement pour confirmer votre rendez-vous.</p>
            </div>
            
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            
            <div class="mt-5">
                <a href="{{ route('reservations.public.form') }}" class="btn btn-primary">
                    <i class="fas fa-calendar-plus me-2"></i> Faire une autre réservation
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} SPA - Tous droits réservés</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
