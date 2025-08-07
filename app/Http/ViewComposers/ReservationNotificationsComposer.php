<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Reservation;

class ReservationNotificationsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Compte uniquement les réservations client non lues
        // client_created = true signifie que la réservation a été faite par un client et non par l'admin
        $newReservationsCount = Reservation::where('is_read', false)
                                ->where('client_created', true)
                                ->count();
        
        // Partage la variable avec la vue
        $view->with('newReservationsCount', $newReservationsCount);
    }
}
