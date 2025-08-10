<?php

namespace App\Services;

class PriorityDetectionService
{
    /**
     * Liste des mots-clés qui indiquent qu'un message est prioritaire.
     * Ces mots seront recherchés dans le sujet et le contenu du message.
     */
    protected $priorityKeywords = [
        // Urgence
        'urgent', 'urgence', 'immédiat', 'immediatement', 'grave',
        // Problèmes
        'problème', 'probleme', 'erreur', 'panne', 'dysfonctionnement',
        // Insatisfaction
        'mécontent', 'mecontent', 'insatisfait', 'déçu', 'decu', 'plainte',
        'remboursement', 'annuler', 'annulation',
        // Santé
        'allergie', 'allergique', 'brûlure', 'brulure', 'irritation',
        'douleur', 'mal', 'blessure',
        // Service client
        'réclamation', 'reclamation', 'important', 'prioritaire',
        // Problèmes financiers
        'facturation', 'double facturation', 'surfacturation', 'erreur de paiement',
        // Dégâts
        'cassé', 'casse', 'endommage', 'endommagé', 'perdu', 'volé', 'vole'
    ];
    
    /**
     * Vérifie si un message contient des mots-clés prioritaires.
     *
     * @param string $subject Le sujet du message
     * @param string $message Le contenu du message
     * @return bool True si le message est prioritaire, false sinon
     */
    public function isPriority(string $subject, string $message): bool
    {
        // Conversion en minuscules pour la recherche insensible à la casse
        $subject = mb_strtolower($subject);
        $message = mb_strtolower($message);
        
        // Texte complet à analyser (sujet + message)
        $fullText = $subject . ' ' . $message;
        
        // Recherche des mots-clés prioritaires
        foreach ($this->priorityKeywords as $keyword) {
            // Recherche du mot-clé avec une correspondance de mot entier
            if ($this->containsWholeWord($fullText, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si le texte contient un mot entier spécifique.
     *
     * @param string $text Le texte dans lequel chercher
     * @param string $word Le mot à rechercher
     * @return bool True si le mot est trouvé, false sinon
     */
    protected function containsWholeWord(string $text, string $word): bool
    {
        // Utilise une expression régulière pour trouver le mot entier
        // \b marque la limite d'un mot
        return preg_match('/\b' . preg_quote($word, '/') . '\b/u', $text) === 1;
    }
}
