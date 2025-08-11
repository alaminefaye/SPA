const NOTIFICATION_SOUND = new AudioContext();
const gainNode = NOTIFICATION_SOUND.createGain();
gainNode.connect(NOTIFICATION_SOUND.destination);

function beep(frequency = 750, duration = 200, type = "sine", volume = 0.5) {
  const oscillator = NOTIFICATION_SOUND.createOscillator();
  oscillator.type = type;
  oscillator.frequency.value = frequency;
  oscillator.connect(gainNode);
  
  // Set volume
  gainNode.gain.value = volume;
  
  oscillator.start();
  setTimeout(() => {
    oscillator.stop();
  }, duration);
}

// Variables pour contrôler les bips répétés
const MAX_BEEPS = 10;
const BEEP_INTERVAL = 3000; // 3 secondes entre chaque bip en millisecondes

// Registre global des bips en cours pour éviter les doublons
window.beepRegistry = window.beepRegistry || {
  activeBeeps: {},
  beepsPlayed: {},
  lastBeepTime: {},
  beepIntervals: {}
};

// Export beep function
window.playNotificationBeep = function() {
  beep(750, 300, "sine", 0.5);
  setTimeout(() => beep(750, 300, "sine", 0.5), 500);
  setTimeout(() => beep(750, 300, "sine", 0.5), 1000);
};

// Fonction pour jouer une série de bips avec intervalles
window.playRepeatedBeep = function(seanceId, force = false) {
  const registry = window.beepRegistry;
  
  // Ne pas redémarrer un bip déjà en cours pour la même séance
  if (registry.activeBeeps[seanceId] && !force) {
    console.log(`Bip déjà actif pour séance ${seanceId} - ignoré`);
    return;
  }

  // Initialiser ou réinitialiser le compteur pour cette séance
  if (force || !registry.beepsPlayed[seanceId]) {
    registry.beepsPlayed[seanceId] = 0;
  }
  
  // Marquer comme actif
  registry.activeBeeps[seanceId] = true;
  
  // Fonction pour jouer un bip unique et incrémenter le compteur
  const playBeep = function() {
    const currentTime = Date.now();
    const canPlayBeep = !registry.lastBeepTime[seanceId] || 
                       (currentTime - registry.lastBeepTime[seanceId] >= BEEP_INTERVAL);
    
    if (canPlayBeep && registry.beepsPlayed[seanceId] < MAX_BEEPS) {
      window.playNotificationBeep();
      registry.beepsPlayed[seanceId]++;
      registry.lastBeepTime[seanceId] = currentTime;
      console.log(`Bip joué pour séance ${seanceId} - ${registry.beepsPlayed[seanceId]}/${MAX_BEEPS} à ${new Date().toLocaleTimeString()}`);
      
      // Si on a atteint le maximum de bips, arrêter
      if (registry.beepsPlayed[seanceId] >= MAX_BEEPS) {
        window.stopRepeatedBeep(seanceId);
      }
    }
  };
  
  // Jouer immédiatement le premier bip
  playBeep();
  
  // Configurer l'intervalle pour les bips suivants
  registry.beepIntervals[seanceId] = setInterval(playBeep, BEEP_INTERVAL);
};

// Fonction pour arrêter une série de bips
window.stopRepeatedBeep = function(seanceId) {
  const registry = window.beepRegistry;
  
  if (registry.beepIntervals[seanceId]) {
    clearInterval(registry.beepIntervals[seanceId]);
    delete registry.beepIntervals[seanceId];
    registry.activeBeeps[seanceId] = false;
    console.log(`Bips arrêtés pour séance ${seanceId}`);
  }
};
