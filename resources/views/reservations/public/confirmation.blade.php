<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation confirmée | Jared SPA</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom styles -->
    <style>
        :root {
            --primary-color: #ff69b4;
            --primary-light: #ff8dc3;
            --primary-dark: #e5007d;
            --secondary-color: #17a2b8;
            --text-dark: #343a40;
            --text-light: #f8f9fa;
            --border-radius: 10px;
            --box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
        }

        .card {
            max-width: 800px;
            margin: 80px auto;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .success-icon-container {
            text-align: center;
            padding: 40px 20px 20px;
        }

        .success-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 100px;
            background-color: #4CAF50;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .success-icon i {
            font-size: 50px;
            color: white;
        }

        .card-body {
            padding: 30px;
            text-align: center;
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .alert-info {
            background-color: rgba(23, 162, 184, 0.15);
            border: none;
            border-radius: var(--border-radius);
            padding: 20px;
            margin: 20px 0;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 5px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        footer {
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
        <div class="card">
            <div class="success-icon-container">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
            
            <div class="card-body">
                <h1>Votre demande de réservation a été envoyée !</h1>
                
                <p class="lead mb-4">
                    Merci d'avoir choisi nos services. Votre demande de réservation a été enregistrée avec succès.
                </p>
                
                <div class="alert alert-info">
                    <p class="mb-0">Notre équipe va examiner votre demande et vous contactera très prochainement pour confirmer votre rendez-vous.</p>
                </div>
                
                <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
                
                <div class="mt-5">
                    <a href="{{ route('reservations.public.form') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i> Faire une autre réservation
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} Jared SPA - Tous droits réservés</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
