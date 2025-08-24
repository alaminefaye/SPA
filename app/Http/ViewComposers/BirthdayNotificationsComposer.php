<?php

namespace App\Http\ViewComposers;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\View\View;

class BirthdayNotificationsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Récupérer tous les anniversaires d'aujourd'hui
        $todayBirthdaysCount = Client::whereNotNull('date_naissance')
            ->whereMonth('date_naissance', Carbon::now()->month)
            ->whereDay('date_naissance', Carbon::now()->day)
            ->count();
            
        // Récupérer la dernière consultation des anniversaires
        $lastViewed = session('last_birthdays_viewed');
        
        // Si la page a déjà été consultée aujourd'hui, ne pas afficher le badge
        $showBadge = true;
        if ($lastViewed) {
            // Convertir en Carbon si ce n'est pas déjà le cas
            if (!($lastViewed instanceof Carbon)) {
                $lastViewed = Carbon::parse($lastViewed);
            }
            
            // Vérifier si la dernière consultation est aujourd'hui
            if ($lastViewed->isToday()) {
                $showBadge = false;
            }
        }
        
        // N'afficher le badge que s'il y a des anniversaires ET que l'utilisateur n'a pas encore consulté la page aujourd'hui
        $displayCount = ($showBadge && $todayBirthdaysCount > 0) ? $todayBirthdaysCount : 0;
        
        $view->with('todayBirthdaysCount', $displayCount);
    }
}
