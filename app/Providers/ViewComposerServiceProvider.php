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
        // Enregistrement des ViewComposers pour les notifications
        view()->composer('layouts.app', \App\Http\ViewComposers\ReservationNotificationsComposer::class);
        view()->composer('layouts.app', \App\Http\ViewComposers\FeedbackNotificationsComposer::class);
    }
}
