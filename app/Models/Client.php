<?php

namespace App\Models;

use Carbon\Carbon;
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
        'date_naissance',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_naissance' => 'date',
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
            ->logOnly(['nom_complet', 'numero_telephone', 'adresse_mail', 'points', 'date_naissance'])
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

    /**
     * Vérifie si c'est l'anniversaire du client aujourd'hui
     * 
     * @return bool
     */
    public function isAnniversaireToday(): bool
    {
        if (!$this->date_naissance) {
            return false;
        }
        
        return $this->date_naissance->format('d-m') === now()->format('d-m');
    }
    
    /**
     * Obtient le nombre de jours avant le prochain anniversaire
     * 
     * @return int|null Retourne null si pas de date de naissance, sinon le nombre de jours
     */
    public function joursAvantAnniversaire(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }
        
        $today = now();
        $birthdayThisYear = Carbon::createFromDate(
            $today->year,
            $this->date_naissance->month,
            $this->date_naissance->day
        );
        
        if ($birthdayThisYear->isPast()) {
            $birthdayThisYear->addYear();
        }
        
        return $today->diffInDays($birthdayThisYear, false);
    }
    
    /**
     * Retourne la date d'anniversaire formatée
     * 
     * @return string|null
     */
    public function getDateAnniversaireFormattee(): ?string
    {
        return $this->date_naissance ? $this->date_naissance->format('d/m/Y') : null;
    }
}
