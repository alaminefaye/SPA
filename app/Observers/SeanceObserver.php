<?php

namespace App\Observers;

use App\Models\Seance;

class SeanceObserver
{
    /**
     * Handle the Seance "creating" event.
     * Génère un numéro de séance automatique au format 0000001
     */
    public function creating(Seance $seance): void
    {
        // Ne générer un numéro que si aucun n'est déjà défini
        if (empty($seance->numero_seance)) {
            // Récupérer la dernière séance pour déterminer le prochain numéro
            $lastSeance = Seance::orderBy('id', 'desc')->first();
            
            // Calculer le prochain numéro
            $nextNumber = 1; // Par défaut, commencer à 1
            
            if ($lastSeance && $lastSeance->numero_seance) {
                // Extraire le nombre du numéro de séance précédent et l'incrémenter
                $lastNumber = (int) $lastSeance->numero_seance;
                $nextNumber = $lastNumber + 1;
            }
            
            // Formater le numéro avec des zéros (0000001)
            $seance->numero_seance = str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Handle the Seance "updated" event.
     */
    public function updated(Seance $seance): void
    {
        //
    }

    /**
     * Handle the Seance "deleted" event.
     */
    public function deleted(Seance $seance): void
    {
        //
    }

    /**
     * Handle the Seance "restored" event.
     */
    public function restored(Seance $seance): void
    {
        //
    }

    /**
     * Handle the Seance "force deleted" event.
     */
    public function forceDeleted(Seance $seance): void
    {
        //
    }
}
