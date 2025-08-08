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

// Export beep function
window.playNotificationBeep = function() {
  beep(750, 300, "sine", 0.5);
  setTimeout(() => beep(750, 300, "sine", 0.5), 500);
  setTimeout(() => beep(750, 300, "sine", 0.5), 1000);
};
