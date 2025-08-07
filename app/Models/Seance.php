<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seance extends Model
{
    protected $fillable = [
        'client_id',
        'salon_id',
        'prestation_id',
        'prix',
        'duree',
        'statut',
        'commentaire',
    ];
    
    protected $casts = [
        'duree' => 'datetime:H:i:s',
        'prix' => 'decimal:2',
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
}
