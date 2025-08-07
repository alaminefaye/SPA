<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Enregistrement du ViewComposer pour les notifications de réservation
        view()->composer('layouts.app', \App\Http\ViewComposers\ReservationNotificationsComposer::class);
    }
}
