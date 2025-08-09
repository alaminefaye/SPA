// service-worker.js - Gestion des notifications pour SPA Jared
self.addEventListener('install', function(event) {
  console.log('Service Worker installé');
  self.skipWaiting();
});

self.addEventListener('activate', function(event) {
  console.log('Service Worker activé');
  return self.clients.claim();
});

// Gestion des notifications push
self.addEventListener('push', function(event) {
  console.log('Notification reçue:', event.data.text());
  
  const data = JSON.parse(event.data.text());
  
  const options = {
    body: data.body,
    icon: '/favicon.ico',
    badge: '/favicon.ico',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      url: data.url
    }
  };
  
  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// Gestion du clic sur une notification
self.addEventListener('notificationclick', function(event) {
  const notification = event.notification;
  const url = notification.data.url;
  
  notification.close();
  
  event.waitUntil(
    clients.openWindow(url)
  );
});

// Écouteur pour les messages du thread principal
self.addEventListener('message', function(event) {
  console.log('Service Worker a reçu un message:', event.data);
  
  // Si le message concerne une alerte de séance terminée
  if (event.data.type === 'seance-alert') {
    // Créer une notification pour l'utilisateur
    self.registration.showNotification('Alerte Séance', {
      body: `La séance ${event.data.seanceName} est terminée!`,
      icon: '/favicon.ico',
      vibrate: [200, 100, 200, 100, 200],
      tag: 'seance-alert',
      renotify: true
    });
    
    // Envoyer un message à toutes les fenêtres ouvertes pour jouer le son
    self.clients.matchAll().then(clients => {
      clients.forEach(client => {
        client.postMessage({
          type: 'play-sound',
          soundType: 'seance-end'
        });
      });
    });
  }
});
