<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Client extends Model
{
    use LogsActivity;
    protected $fillable = [
        'nom_complet',
        'numero_telephone',
        'adresse_mail',
        'points',
    ];
    
    /**
     * Get the seances for the client.
     */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }
    
    /**
     * Ajoute des points de fidélité au client
     * @param int $points Nombre de points à ajouter
     * @return bool
     */
    public function ajouterPoints(int $points = 1): bool
    {
        $this->points += $points;
        return $this->save();
    }
    
    /**
     * Utilise des points de fidélité pour obtenir une séance gratuite
     * @param int $pointsToUse Nombre de points à utiliser (défaut: 5)
     * @return bool True si les points ont été utilisés avec succès, false sinon
     */
    public function utiliserPoints(int $pointsToUse = 5): bool
    {
        if ($this->points >= $pointsToUse) {
            $this->points -= $pointsToUse;
            return $this->save();
        }
        
        return false;
    }
    
    /**
     * Vérifie si le client a suffisamment de points pour une séance gratuite
     * @param int $pointsRequired Nombre de points requis (défaut: 5)
     * @return bool
     */
    public function peutObtenirSeanceGratuite(int $pointsRequired = 5): bool
    {
        return $this->points >= $pointsRequired;
    }
    
    /**
     * Configure les options de journalisation d'activité
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom_complet', 'numero_telephone', 'adresse_mail', 'points'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                $desc = "Le client " . $this->nom_complet . " a été ";
                switch($eventName) {
                    case 'created':
                        $desc = "Nouveau client créé : " . $this->nom_complet;
                        break;
                    case 'updated':
                        $desc = "Informations du client " . $this->nom_complet . " modifiées";
                        break;
                    case 'deleted':
                        $desc = "Client supprimé : " . $this->nom_complet;
                        break;
                }
                return $desc;
            });
    }
}
