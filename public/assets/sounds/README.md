# Sons pour les notifications

Ce dossier contient les sons utilisés pour les notifications dans l'application.

## Sons requis

- `alert.mp3` : Son d'alerte pour les séances terminées

## Comment obtenir les sons

Vous pouvez utiliser des sons libres de droits disponibles sur des sites comme :
- [Freesound.org](https://freesound.org/)
- [Mixkit](https://mixkit.co/free-sound-effects/)
- [Soundsnap](https://www.soundsnap.com/)

Un bon choix serait un son de cloche ou de carillon pour notifier la fin d'une séance.

## Installation

1. Téléchargez un son d'alerte au format MP3
2. Renommez-le en `alert.mp3`
3. Placez-le dans ce dossier (`public/assets/sounds/`)

## Test

Pour tester si le son fonctionne correctement, vous pouvez utiliser la console du navigateur et exécuter :

```javascript
const sound = new Audio('/assets/sounds/alert.mp3');
sound.play();
```
