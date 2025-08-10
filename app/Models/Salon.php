<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salon extends Model
{
    protected $fillable = [
        'nom',
    ];
    
    /**
     * Get the seances associated with the salon.
     */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }
    
    /**
     * Vérifie si le salon est disponible (aucune séance active)
     * Un salon est considéré comme occupé s'il a des séances en statut 'planifiee' ou 'en_cours'
     * 
     * @return bool
     */
    public function estDisponible(): bool
    {
        return !$this->seances()
            ->whereIn('statut', ['planifiee', 'en_cours'])
            ->exists();
    }
    
    /**
     * Récupère tous les salons disponibles
     * 
     * @return array
     */
    public static function getSalonsDisponibles()
    {
        $salonsDisponibles = [];
        $salons = self::all();
        
        foreach ($salons as $salon) {
            if ($salon->estDisponible()) {
                $salonsDisponibles[] = $salon;
            }
        }
        
        return $salonsDisponibles;
    }
}
