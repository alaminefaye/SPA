// notification-manager.js - Gestion des alertes et sons pour l'application SPA

class NotificationManager {
    constructor() {
        this.swRegistration = null;
        this.isNotificationSupported = 'serviceWorker' in navigator && 'Notification' in window;
        this.alertSound = new Audio('/assets/sounds/alert.mp3');
        this.seancesData = [];
        this.checkInterval = null;
        this.alertIntervals = {}; // Pour stocker les intervalles de répétition d'alerte
        this.setupListeners();
    }

    // Initialiser le gestionnaire de notifications
    async init() {
        if (!this.isNotificationSupported) {
            console.warn('Les notifications ne sont pas prises en charge par ce navigateur');
            return;
        }

        // Demander la permission pour les notifications
        if (Notification.permission === 'default') {
            await Notification.requestPermission();
        }

        // Enregistrer le service worker
        try {
            this.swRegistration = await navigator.serviceWorker.register('/service-worker.js');
            console.log('Service Worker enregistré avec succès:', this.swRegistration);
            
            // Configurer les écouteurs d'événements pour le service worker
            navigator.serviceWorker.addEventListener('message', this.handleServiceWorkerMessage.bind(this));
            
            // Synchroniser les séances avec le serveur
            this.syncSeancesData();
            
            // Démarrer la vérification périodique des séances
            this.startPeriodicCheck();
        } catch (error) {
            console.error('Erreur lors de l\'enregistrement du Service Worker:', error);
        }
    }

    // Configurer les écouteurs d'événements
    setupListeners() {
        // Écouteur pour les événements de visibilité de la page
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.syncSeancesData();
            }
        });

        // Écouteur pour le stockage local (pour la communication entre onglets)
        window.addEventListener('storage', (event) => {
            if (event.key === 'spa_seance_alert') {
                const data = JSON.parse(event.newValue);
                this.playAlertSound();
            }
        });
    }

    // Gérer les messages du service worker
    handleServiceWorkerMessage(event) {
        console.log('Message reçu du Service Worker:', event.data);
        
        if (event.data.type === 'play-sound') {
            this.playAlertSound();
        }
    }

    // Jouer le son d'alerte
    playAlertSound(seanceId = null) {
        // Réinitialiser le son pour qu'il puisse être joué plusieurs fois
        this.alertSound.pause();
        this.alertSound.currentTime = 0;
        
        // Jouer le son avec promesse
        const playPromise = this.alertSound.play();
        
        if (playPromise !== undefined) {
            playPromise.catch(error => {
                console.warn('La lecture automatique audio a été empêchée:', error);
                // Afficher un bouton pour permettre à l'utilisateur de jouer le son manuellement
                this.showPlayButton(seanceId);
            });
        }
    }

    // Afficher un bouton pour jouer le son manuellement (si autoplay est bloqué)
    showPlayButton(seanceId = null) {
        // Vérifier si la notification visuelle existe déjà
        let notif = document.getElementById('sound-notification');
        
        if (!notif) {
            notif = document.createElement('div');
            notif.id = 'sound-notification';
            notif.style.position = 'fixed';
            notif.style.bottom = '20px';
            notif.style.right = '20px';
            notif.style.backgroundColor = '#ff6b6b';
            notif.style.color = 'white';
            notif.style.padding = '10px 15px';
            notif.style.borderRadius = '5px';
            notif.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
            notif.style.zIndex = '9999';
            notif.style.display = 'flex';
            notif.style.alignItems = 'center';
            notif.style.gap = '10px';
            
            const icon = document.createElement('i');
            icon.className = 'bx bx-bell';
            icon.style.fontSize = '24px';
            
            const text = document.createElement('span');
            text.textContent = 'Une séance est terminée!';
            
            const button = document.createElement('button');
            button.textContent = 'Jouer l\'alerte';
            button.className = 'btn btn-sm btn-light';
            button.onclick = () => {
                this.playAlertSound();
                setTimeout(() => notif.remove(), 3000);
            };
            
            const closeBtn = document.createElement('i');
            closeBtn.className = 'bx bx-x';
            closeBtn.style.marginLeft = 'auto';
            closeBtn.style.cursor = 'pointer';
            closeBtn.onclick = () => notif.remove();
            
            notif.appendChild(icon);
            notif.appendChild(text);
            notif.appendChild(button);
            notif.appendChild(closeBtn);
            
            document.body.appendChild(notif);
        }
    }

    // Synchroniser les données des séances avec le serveur
    syncSeancesData() {
        fetch('/api/seances/en-cours', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.seancesData = data.seances;
                this.checkSeancesStatus();
            }
        })
        .catch(error => {
            console.error('Erreur lors de la synchronisation des séances:', error);
        });
    }

    // Démarrer la vérification périodique des séances
    startPeriodicCheck() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
        
        // Vérifier toutes les 5 secondes pour plus de réactivité
        this.checkInterval = setInterval(() => {
            this.syncSeancesData();
        }, 5000);
    }

    // Vérifier le statut des séances en cours
    checkSeancesStatus() {
        const now = new Date();
        
        this.seancesData.forEach(seance => {
            // Calculer le temps restant pour chaque séance
            const heureDebut = new Date(seance.heure_debut);
            const dureeMinutes = seance.duree_minutes;
            const finPrevue = new Date(heureDebut);
            finPrevue.setMinutes(finPrevue.getMinutes() + dureeMinutes);
            
            // Calculer la différence en secondes
            const diffSeconds = (finPrevue - now) / 1000;
            
            // Si le temps est écoulé exactement (différence ≤ 0)
            // ET que la séance est toujours marquée "en_cours" 
            // ET qu'il n'y a pas déjà une alerte active pour cette séance
            if (diffSeconds <= 0 && seance.statut === 'en_cours' && !this.alertIntervals[seance.id]) {
                console.log(`Séance #${seance.id} terminée - Déclenchement de l'alerte`);
                this.notifySeanceEnded(seance);
            }
        });
    }

    // Vérifier immédiatement si des séances sont terminées 
    // (sans attendre l'intervalle de vérification)
    checkSeancesImmediate() {
        console.log("Vérification immédiate des séances terminées");
        fetch('/api/seances/en-cours')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.seances) {
                    this.seancesData = data.seances;
                    // Vérifier immédiatement si des séances sont terminées
                    this.checkSeancesStatus();
                }
            })
            .catch(error => console.error('Erreur lors de la récupération des séances en cours:', error));
    }

    // Notifier la fin d'une séance
    notifySeanceEnded(seance) {
        const seanceId = seance.id;
        console.log(`Séance #${seanceId} terminée - Déclenchement de la notification sonore`);
        
        // Arrêter l'intervalle existant s'il y en a un
        if (this.alertIntervals[seanceId]) {
            clearInterval(this.alertIntervals[seanceId]);
        }
        
        // Jouer le son immédiatement
        this.playAlertSound(seanceId);
        
        // Créer un intervalle pour rejouer le son toutes les 3 secondes
        this.alertIntervals[seanceId] = setInterval(() => {
            // Vérifier si la séance est encore en cours avant de jouer le son
            this.checkSeanceStatus(seanceId).then(isActive => {
                if (isActive) {
                    // Si la séance est toujours active, jouer le son
                    this.playAlertSound(seanceId);
                } else {
                    // Si la séance est terminée, arrêter l'intervalle
                    clearInterval(this.alertIntervals[seanceId]);
                    delete this.alertIntervals[seanceId];
                    console.log(`Alerte arrêtée pour la séance #${seanceId} (séance terminée)`);
                }
            });
        }, 3000); // Répéter toutes les 3 secondes
        
        // Enregistrer dans le stockage local pour notifier les autres onglets
        localStorage.setItem('spa_seance_alert', JSON.stringify({
            id: seance.id,
            timestamp: new Date().toISOString()
        }));
        
        // Envoyer au service worker si disponible
        if (this.swRegistration && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'seance-alert',
                seanceId: seance.id,
                seanceName: seance.client_nom || `Séance #${seance.id}`
            });
        }
        
        // Afficher une notification du navigateur si autorisé
        if (Notification.permission === 'granted') {
            const notifOptions = {
                body: `La séance de ${seance.client_nom || 'Client'} est terminée!`,
                icon: '/favicon.ico',
                vibrate: [100, 50, 100],
                tag: 'seance-end',
                renotify: true
            };
            
            new Notification('Fin de Séance', notifOptions);
        }
    }
}

// Initialisation du gestionnaire de notifications
document.addEventListener('DOMContentLoaded', () => {
    window.notificationManager = new NotificationManager();
    window.notificationManager.init();
    
    // Vérifier immédiatement les séances toutes les 15 secondes (en plus de l'intervalle de 5s)
    setInterval(() => {
        if (window.notificationManager) {
            window.notificationManager.checkSeancesImmediate();
        }
    }, 15000);
});
