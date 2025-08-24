<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Feedback;

class CheckFeedbackRatings extends Command
{
    protected $signature = 'check:feedback-ratings';
    protected $description = 'Vérifie si la colonne satisfaction_rating existe et affiche les données';

    public function handle()
    {
        // Vérifier l'existence de la colonne
        $hasColumn = Schema::hasColumn('feedbacks', 'satisfaction_rating');
        $this->info('La colonne satisfaction_rating existe dans la table feedbacks: ' . ($hasColumn ? 'Oui' : 'Non'));
        
        if ($hasColumn) {
            // Afficher les feedbacks avec leur satisfaction_rating
            $feedbacks = Feedback::select('id', 'sujet', 'satisfaction_rating')->get();
            
            $this->table(
                ['ID', 'Sujet', 'Satisfaction Rating'],
                $feedbacks->map(function ($feedback) {
                    return [
                        'ID' => $feedback->id,
                        'Sujet' => $feedback->sujet,
                        'Satisfaction Rating' => $feedback->satisfaction_rating
                    ];
                })
            );

            // Compter les feedbacks par rating
            $ratings = Feedback::selectRaw('satisfaction_rating, COUNT(*) as count')
                ->groupBy('satisfaction_rating')
                ->get();
                
            $this->info('Distribution des évaluations:');
            foreach ($ratings as $rating) {
                $this->line("Rating {$rating->satisfaction_rating}: {$rating->count} feedback(s)");
            }
        }
    }
}
