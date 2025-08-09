#!/bin/bash

# Script pour télécharger un son d'alerte pour l'application SPA
# Ce script télécharge un son de notification gratuit et l'installe dans le bon dossier

# Définir les chemins
SOUND_DIR="./public/assets/sounds"
TARGET_FILE="$SOUND_DIR/alert.mp3"

echo "Téléchargement du fichier son d'alerte..."

# URL d'un son de notification libre de droits (bell sound)
SOUND_URL="https://cdn.freesound.org/previews/337/337049_3232293-lq.mp3"

# Créer le répertoire si nécessaire
mkdir -p "$SOUND_DIR"

# Télécharger le fichier
curl -L "$SOUND_URL" -o "$TARGET_FILE"

# Vérifier si le téléchargement a réussi
if [ -f "$TARGET_FILE" ] && [ -s "$TARGET_FILE" ]; then
    echo "✓ Son d'alerte téléchargé avec succès dans $TARGET_FILE"
    echo "✓ Taille du fichier: $(du -h "$TARGET_FILE" | cut -f1) octets"
    echo ""
    echo "Vous pouvez maintenant tester le système de notifications à /notifications-test"
else
    echo "❌ Échec du téléchargement. Veuillez vérifier votre connexion internet."
fi
