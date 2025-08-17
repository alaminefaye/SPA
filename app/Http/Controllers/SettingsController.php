<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Affiche la page des paramètres
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        // Ici, vous pourriez récupérer des paramètres spécifiques à l'utilisateur
        // ou des paramètres globaux de l'application
        
        // Pour l'instant, nous allons simplement passer l'utilisateur à la vue
        return view('settings.index', compact('user'));
    }

    /**
     * Met à jour les paramètres de l'utilisateur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validation des données reçues
        $request->validate([
            'notification_email' => ['nullable', 'boolean'],
            'notification_app' => ['nullable', 'boolean'],
            'theme' => ['nullable', 'string', 'in:light,dark,auto'],
            'language' => ['nullable', 'string', 'in:fr,en'],
        ]);
        
        // Pour cet exemple, nous allons stocker les paramètres dans une table de session
        // Dans une implémentation réelle, vous pourriez utiliser une table de paramètres utilisateur
        
        $settings = [
            'notification_email' => $request->has('notification_email'),
            'notification_app' => $request->has('notification_app'),
            'theme' => $request->theme ?? 'light',
            'language' => $request->language ?? 'fr',
        ];
        
        // Stockage dans la session pour l'instant
        session(['user_settings' => $settings]);
        
        // Dans une implémentation réelle, vous pourriez avoir un modèle UserSetting
        // $user = Auth::user();
        // $user->settings()->update($settings);
        
        return redirect()->route('settings.index')->with('success', 'Paramètres mis à jour avec succès');
    }
}
