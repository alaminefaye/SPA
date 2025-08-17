<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use App\Models\User;
use Illuminate\Http\Request;

class LoginActivityController extends Controller
{
    /**
     * Affiche la liste des activités de connexion.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = LoginActivity::with('user');
        
        // Filtrage par utilisateur
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filtrage par statut de connexion
        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('successful', true);
            } elseif ($request->status === 'failed') {
                $query->where('successful', false);
            }
        }
        
        // Recherche par adresse IP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('device', 'like', "%{$search}%")
                  ->orWhere('browser', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Tri des résultats
        $query->orderBy('login_at', 'desc');
        
        $activities = $query->paginate(15)->withQueryString();
        $users = User::orderBy('name')->get();
        
        return view('admin.login-activities.index', compact('activities', 'users'));
    }
    
    /**
     * Affiche les détails d'une activité de connexion.
     *
     * @param  \App\Models\LoginActivity  $activity
     * @return \Illuminate\View\View
     */
    public function show(LoginActivity $loginActivity)
    {
        return view('admin.login-activities.show', ['activity' => $loginActivity]);
    }
    
    /**
     * Supprime une activité de connexion.
     *
     * @param  \App\Models\LoginActivity  $activity
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LoginActivity $loginActivity)
    {
        $loginActivity->delete();
        
        return redirect()->route('login-activities.index')
            ->with('success', 'Activité de connexion supprimée avec succès.');
    }
    
    /**
     * Efface toutes les activités de connexion.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearAll()
    {
        LoginActivity::truncate();
        
        return redirect()->route('login-activities.index')
            ->with('success', 'Toutes les activités de connexion ont été supprimées.');
    }
}
