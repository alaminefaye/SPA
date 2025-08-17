<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class LoyaltyPointsController extends Controller
{
    /**
     * Affiche la liste des clients avec leurs points de fidélité
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Client::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom_complet', 'LIKE', "%{$search}%")
                  ->orWhere('numero_telephone', 'LIKE', "%{$search}%")
                  ->orWhere('adresse_mail', 'LIKE', "%{$search}%");
            });
        }
        
        $clients = $query->orderBy('points', 'desc')->paginate(10);
        
        // Statistiques des points de fidélité
        $statsPoints = DB::table('clients')
            ->select(
                DB::raw('SUM(points) as total_points'),
                DB::raw('AVG(points) as moyenne_points'),
                DB::raw('MAX(points) as max_points'),
                DB::raw('COUNT(CASE WHEN points >= 5 THEN 1 END) as eligible_seance_gratuite')
            )
            ->first();
        
        return view('clients.loyalty_points.index', compact('clients', 'statsPoints'));
    }
    
    /**
     * Affiche le formulaire pour ajouter/retirer des points
     *
     * @param Client $client
     * @return \Illuminate\View\View
     */
    public function edit(Client $client)
    {
        return view('clients.loyalty_points.edit', compact('client'));
    }
    
    /**
     * Met à jour les points de fidélité d'un client
     *
     * @param Request $request
     * @param Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'action' => 'required|in:add,remove,set',
            'points' => 'required|integer|min:1',
            'raison' => 'nullable|string|max:255',
        ]);
        
        $originalPoints = $client->points;
        
        switch ($request->action) {
            case 'add':
                $client->ajouterPoints($request->points);
                $message = "{$request->points} point(s) ajouté(s) au client {$client->nom_complet}";
                break;
                
            case 'remove':
                if ($client->points >= $request->points) {
                    $client->points -= $request->points;
                    $client->save();
                    $message = "{$request->points} point(s) retiré(s) au client {$client->nom_complet}";
                } else {
                    return redirect()->back()->with('error', 'Le client ne possède pas assez de points.');
                }
                break;
                
            case 'set':
                $client->points = $request->points;
                $client->save();
                $message = "Points du client {$client->nom_complet} définis à {$request->points}";
                break;
        }
        
        // Enregistrement de l'activité avec plus de détails
        activity()
            ->performedOn($client)
            ->withProperties([
                'original_points' => $originalPoints,
                'new_points' => $client->points,
                'action' => $request->action,
                'raison' => $request->raison ?? 'Aucune raison spécifiée'
            ])
            ->log("Points de fidélité modifiés: $message");
        
        return redirect()->route('loyalty-points.index')->with('success', $message);
    }
    
    /**
     * Affiche l'historique des modifications de points pour un client
     *
     * @param Client $client
     * @return \Illuminate\View\View
     */
    public function history(Client $client)
    {
        $activities = Activity::query()
            ->where('subject_type', Client::class)
            ->where('subject_id', $client->id)
            ->where(function ($query) {
                $query->where('description', 'LIKE', '%points de fidélité%')
                      ->orWhereRaw("JSON_EXTRACT(properties, '$.original_points') != JSON_EXTRACT(properties, '$.new_points')");
            })
            ->latest()
            ->paginate(15);
            
        return view('clients.loyalty_points.history', compact('client', 'activities'));
    }
}
