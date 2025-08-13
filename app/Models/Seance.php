<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Seance extends Model
{
    use LogsActivity;
    // Définition des constantes de statut
    const STATUT_PLANIFIEE = 'planifiee';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINEE = 'terminee';
    const STATUT_ANNULEE = 'annulee';
    const STATUT_EN_ATTENTE = 'en_attente'; // Nouveau statut pour la file d'attente
    
    protected $fillable = [
        'client_id',
        'salon_id',
        'prestation_id',
        'prix',
        'duree',
        'statut',
        'commentaire',
        'is_free',
        'numero_seance',
    ];
    
    protected $casts = [
        'duree' => 'datetime:H:i:s',
        'prix' => 'decimal:2',
        'is_free' => 'boolean',
    ];
    
    /**
     * Get the client associated with the seance.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Get the salon associated with the seance.
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }
    
    /**
     * Get the prestations associated with the seance.
     */
    public function prestations()
    {
        return $this->belongsToMany(Prestation::class, 'seance_prestation')
                    ->withTimestamps();
    }
    
    /**
     * Calcule le prix total de toutes les prestations associées à la séance
     */
    public function calculerPrixTotal()
    {
        $total = 0;
        foreach ($this->prestations as $prestation) {
            $total += $prestation->prix;
        }
        return $total;
    }
    
    /**
     * Calcule la durée totale de toutes les prestations associées à la séance
     * Renvoie la durée au format H:i:s
     */
    public function calculerDureeTotale()
    {
        $totalMinutes = 0;
        foreach ($this->prestations as $prestation) {
            // Convertir la durée de la prestation en minutes
            $dureePrestation = $prestation->duree;
            $heures = (int) $dureePrestation->format('H');
            $minutes = (int) $dureePrestation->format('i');
            $totalMinutes += ($heures * 60 + $minutes);
        }
        
        // Convertir les minutes totales en format H:i:s
        $heures = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d:00', $heures, $minutes);
    }
    
    /**
     * Trouve la prochaine séance en attente pour un salon spécifique ou pour n'importe quel salon
     *
     * @param int|null $salon_id ID du salon, ou null pour n'importe quel salon
     * @return Seance|null
     */
    public static function trouverProchainEnAttente(?int $salon_id = null)
    {
        $query = self::where('statut', self::STATUT_EN_ATTENTE)
                    ->orderBy('created_at', 'asc'); // Plus ancienne séance d'abord (FIFO)
        
        if ($salon_id) {
            // Si un salon spécifique est demandé
            $query->where('salon_id', $salon_id);
        }
        
        return $query->first();
    }
    
    /**
     * Attribue un salon disponible à une séance en attente et la passe en statut "planifiee"
     *
     * @param int $salon_id ID du salon à attribuer
     * @return bool
     */
    public function attribuerSalon(int $salon_id): bool
    {
        if ($this->statut !== self::STATUT_EN_ATTENTE) {
            // Seules les séances en attente peuvent recevoir un salon
            return false;
        }
        
        // Vérifier que le salon existe et est disponible
        $salon = Salon::find($salon_id);
        if (!$salon || !$salon->estDisponible()) {
            return false;
        }
        
        // Attribuer le salon à la séance
        $this->salon_id = $salon_id;
        $this->statut = self::STATUT_PLANIFIEE;
        
        // Ajouter un commentaire explicatif
        $commentaire = "\n[AUTO] Séance sortie de file d'attente : salon " . $salon->nom . " attribué automatiquement le " . now()->format('d/m/Y \u00e0 H:i') . "."; 
        $this->commentaire = $this->commentaire 
            ? $this->commentaire . $commentaire 
            : $commentaire;
        
        return $this->save();
    }
    
    /**
     * Configure les options de journalisation d'activité
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['client_id', 'salon_id', 'prix', 'duree', 'statut', 'commentaire', 'is_free', 'numero_seance', 'date_seance', 'heure_prevu'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                $desc = "La séance a été ";
                switch($eventName) {
                    case 'created':
                        $desc .= "créée";
                        break;
                    case 'updated':
                        $desc .= "modifiée";
                        break;
                    case 'deleted':
                        $desc .= "supprimée";
                        break;
                }
                return $desc;
            });
    }
}
