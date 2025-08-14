<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prestation;
use Carbon\Carbon;

class PrestationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Format de durée pour le stockage dans la base de données
        $formatDuree = function($heures, $minutes) {
            return Carbon::createFromTime($heures, $minutes, 0)->format('H:i:s');
        };

        // Liste des prestations à créer
        $prestations = [
            [
                'nom_prestation' => 'Soins de corps + soins de visage',
                'prix' => 20000.00,
                'duree' => $formatDuree(1, 30) // 1h30
            ],
            [
                'nom_prestation' => 'Soins de corps avec sauna',
                'prix' => 10000.00,
                'duree' => $formatDuree(0, 55) // 55min
            ],
            [
                'nom_prestation' => 'Soins de corps simple',
                'prix' => 10000.00,
                'duree' => $formatDuree(0, 40) // 35-40min (moyenne à 40min)
            ],
            [
                'nom_prestation' => 'Épilation aisselle',
                'prix' => 5000.00,
                'duree' => $formatDuree(0, 10) // 10min
            ],
            [
                'nom_prestation' => 'Épilation jambe',
                'prix' => 25000.00,
                'duree' => $formatDuree(0, 35) // 35min
            ],
            [
                'nom_prestation' => 'Épilation bras',
                'prix' => 20000.00, 
                'duree' => $formatDuree(0, 30) // 30min
            ],
            [
                'nom_prestation' => 'Épilation maillot',
                'prix' => 15000.00,
                'duree' => $formatDuree(0, 20) // 20min (estimation basée sur d'autres épilations)
            ],
            [
                'nom_prestation' => 'Vagiacial',
                'prix' => 20000.00,
                'duree' => $formatDuree(0, 30) // 30min (estimation)
            ],
            [
                'nom_prestation' => 'Pédicure manucure',
                'prix' => 15000.00, // Prix estimé car non spécifié
                'duree' => $formatDuree(0, 50) // 45min à 1h (moyenne à 50min)
            ],
            [
                'nom_prestation' => 'Soins amincissant complet',
                'prix' => 25000.00,
                'duree' => $formatDuree(1, 0) // 1h
            ],
            [
                'nom_prestation' => 'Soins de visage avec spa facial approfondi',
                'prix' => 15000.00, // Prix estimé car non spécifié
                'duree' => $formatDuree(0, 40) // 40min
            ],
            [
                'nom_prestation' => 'Soins de corps rituel marocain',
                'prix' => 10000.00,
                'duree' => $formatDuree(1, 0) // 1h
            ],
            [
                'nom_prestation' => 'Soins de corps basic',
                'prix' => 5000.00,
                'duree' => $formatDuree(0, 25) // 25min
            ],
            [
                'nom_prestation' => 'Consultation diététique programme minceur',
                'prix' => 15000.00,
                'duree' => $formatDuree(0, 40) // 40min (estimation)
            ],
            [
                'nom_prestation' => 'Soins peeling anti tache',
                'prix' => 35000.00,
                'duree' => $formatDuree(0, 45) // 45min (estimation)
            ],
            [
                'nom_prestation' => 'Extension de cils Mega russe',
                'prix' => 35000.00,
                'duree' => $formatDuree(2, 0) // 2h (estimation basée sur complexité)
            ],
            [
                'nom_prestation' => 'Extension de cils volume russe',
                'prix' => 30000.00,
                'duree' => $formatDuree(1, 45) // 1h45 (estimation)
            ],
            [
                'nom_prestation' => 'Extension de cils volume glamour',
                'prix' => 25000.00,
                'duree' => $formatDuree(1, 30) // 1h30 (estimation)
            ],
            [
                'nom_prestation' => 'Extension de cils hybride',
                'prix' => 25000.00,
                'duree' => $formatDuree(1, 30) // 1h30 (estimation)
            ],
            [
                'nom_prestation' => 'Extension de cils Classic',
                'prix' => 15000.00,
                'duree' => $formatDuree(1, 0) // 1h (estimation)
            ],
        ];

        // Insertion des prestations dans la base de données
        foreach ($prestations as $prestation) {
            Prestation::create($prestation);
        }
    }
}
