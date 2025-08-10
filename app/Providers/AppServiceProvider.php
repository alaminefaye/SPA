<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\ReservationNotificationsComposer;
use Illuminate\Support\Facades\View;
use App\Models\Seance;
use App\Observers\SeanceObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrement du ViewComposer pour les notifications de réservation
        View::composer('layouts.app', ReservationNotificationsComposer::class);
        
        // Enregistrement de l'observateur pour la génération automatique des numéros de séance
        Seance::observe(SeanceObserver::class);
    }
}
