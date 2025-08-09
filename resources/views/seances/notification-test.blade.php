@extends('layouts.app')

@section('title', 'Test des Notifications')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Séances /</span> Test des Notifications
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Test du système de notifications</h5>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Notifications sonores</h5>
                            <p class="card-text">
                                Testez le système de notifications sonores pour les séances terminées. 
                                Ce système fonctionnera même si vous êtes sur une autre page ou un autre onglet.
                            </p>
                            <button id="test-sound" class="btn btn-primary">
                                <i class="bx bx-bell me-1"></i> Tester le son d'alerte
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Notifications navigateur</h5>
                            <p class="card-text">
                                Testez les notifications système du navigateur. 
                                Vous devez autoriser les notifications dans votre navigateur.
                            </p>
                            <button id="test-notification" class="btn btn-secondary">
                                <i class="bx bx-notification me-1"></i> Tester les notifications
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6><i class="bx bx-info-circle me-1"></i> À propos du système de notifications</h6>
                        <p>
                            Le système de notifications est conçu pour vous alerter lorsqu'une séance est terminée, 
                            même si vous n'êtes pas sur la page des séances en cours. 
                            Voici comment il fonctionne :
                        </p>
                        <ul>
                            <li>Le système vérifie périodiquement les séances en cours</li>
                            <li>Si une séance devrait être terminée, une notification sonore est émise</li>
                            <li>Une notification du navigateur s'affiche également si vous les avez autorisées</li>
                            <li>Cette alerte fonctionne même si vous êtes sur une autre page du site</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tester le son d'alerte
    document.getElementById('test-sound').addEventListener('click', function() {
        // Si le gestionnaire de notifications est disponible
        if (window.notificationManager) {
            window.notificationManager.playAlertSound();
            showStatusMessage('Son d\'alerte joué', 'success');
        } else {
            // Fallback si le gestionnaire n'est pas disponible
            const sound = new Audio('/assets/sounds/alert.mp3');
            sound.play()
                .then(() => {
                    showStatusMessage('Son d\'alerte joué', 'success');
                })
                .catch(error => {
                    console.error('Erreur lors de la lecture du son:', error);
                    showStatusMessage('Erreur: ' + error.message, 'danger');
                });
        }
    });

    // Tester les notifications du navigateur
    document.getElementById('test-notification').addEventListener('click', function() {
        // Vérifier si les notifications sont supportées
        if (!('Notification' in window)) {
            showStatusMessage('Les notifications ne sont pas prises en charge par ce navigateur', 'warning');
            return;
        }

        // Vérifier si l'autorisation est déjà accordée
        if (Notification.permission === 'granted') {
            showNotification();
        } else if (Notification.permission !== 'denied') {
            // Demander l'autorisation
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    showNotification();
                } else {
                    showStatusMessage('Autorisation refusée pour les notifications', 'warning');
                }
            });
        } else {
            showStatusMessage('Notifications bloquées. Veuillez modifier les paramètres de votre navigateur.', 'warning');
        }
    });

    // Fonction pour afficher une notification
    function showNotification() {
        const notification = new Notification('Test de Notification', {
            body: 'Cette notification indique qu\'une séance est terminée',
            icon: '/favicon.ico',
            vibrate: [100, 50, 100]
        });

        showStatusMessage('Notification envoyée', 'success');
    }

    // Afficher un message de statut temporaire
    function showStatusMessage(message, type = 'info') {
        // Vérifier si un message existe déjà
        let existingAlert = document.getElementById('status-message');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Créer le nouvel élément
        const alertDiv = document.createElement('div');
        alertDiv.id = 'status-message';
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Insérer avant le premier élément de la card
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);

        // Supprimer automatiquement après 3 secondes
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 3000);
    }

    // Vérifier si le Service Worker est enregistré
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistration().then(registration => {
            if (registration) {
                console.log('Service Worker enregistré:', registration);
            } else {
                console.warn('Service Worker non enregistré');
            }
        });
    }
});
</script>
@endsection
