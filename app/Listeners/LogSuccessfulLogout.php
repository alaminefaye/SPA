<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        
        if ($user) {
            // Rechercher la dernière activité de connexion de l'utilisateur sans déconnexion
            $loginActivity = LoginActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first();
            
            if ($loginActivity) {
                $loginActivity->update([
                    'logout_at' => Carbon::now()
                ]);
            }
        }
    }
}
