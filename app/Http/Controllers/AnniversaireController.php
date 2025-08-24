<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnniversaireController extends Controller
{
    /**
     * Affiche la liste des clients dont l'anniversaire approche ou est aujourd'hui
     */
    public function index(Request $request)
    {
        // Récupère tous les clients avec une date d'anniversaire définie
        $query = Client::whereNotNull('date_naissance');
        
        // Si recherche par nom ou téléphone
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom_complet', 'LIKE', "%{$search}%")
                  ->orWhere('numero_telephone', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtre optionnel pour la période (aujourd'hui, semaine, mois)
        $periode = $request->input('periode', 'aujourdhui');
        $today = Carbon::now();
        
        // Appliquer le filtre selon la période sélectionnée
        switch ($periode) {
            case 'aujourdhui':
                // Filtre pour aujourd'hui - même jour et même mois
                $query->whereMonth('date_naissance', $today->month)
                      ->whereDay('date_naissance', $today->day);
                break;
            
            case 'semaine':
                // Pour la semaine, on utilise une approche différente
                // Garder la requête générale et on filtrera par code PHP
                break;
                
            case 'mois':
            default:
                // Pour le mois, on filtre sur le même mois uniquement
                $query->whereMonth('date_naissance', $today->month);
                break;
        }
        
        // Obtenir les clients selon le filtre primaire
        $clients = $query->get();
        
        // Traitement spécifique pour le filtre semaine
        if ($periode === 'semaine') {
            // Pour les 7 prochains jours, on filtre manuellement
            $clients = $clients->filter(function($client) use ($today) {
                $birthMonth = $client->date_naissance->format('m');
                $birthDay = $client->date_naissance->format('d');
                $thisYearBirthday = Carbon::create($today->year, $birthMonth, $birthDay, 0, 0, 0);
                
                // Si l'anniversaire est déjà passé cette année, on regarde pour l'année prochaine
                if ($thisYearBirthday->isPast()) {
                    $thisYearBirthday->addYear();
                }
                
                $daysUntilBirthday = $today->diffInDays($thisYearBirthday, false);
                return $daysUntilBirthday >= 0 && $daysUntilBirthday <= 7;
            });
        }
        
        // Trier par proximité de l'anniversaire (jour le plus proche en premier)
        $clientsAnniversaires = $clients->sortBy(function($client) {
            return $client->joursAvantAnniversaire();
        })->values();
        
        // Enregistrer la dernière consultation des anniversaires dans la session
        session(['last_birthdays_viewed' => now()]);
        
        return view('clients.anniversaires.index', compact('clientsAnniversaires', 'search', 'periode'));
    }
}
