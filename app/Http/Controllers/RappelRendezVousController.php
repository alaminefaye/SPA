<?php

namespace App\Http\Controllers;

use App\Models\RappelRendezVous;
use App\Models\Client;
use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RappelRendezVousController extends Controller
{
    /**
     * Display a listing of the rappels de rendez-vous
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'semaine');
        $search = $request->get('search');
        
        $query = RappelRendezVous::with(['client', 'seance'])
                ->where('statut', 'en_attente');
        
        // Filtre de recherche
        if ($search) {
            $query->whereHas('client', function($q) use ($search) {
                $q->where('nom_complet', 'like', "%{$search}%")
                  ->orWhere('numero_telephone', 'like', "%{$search}%");
            });
        }
        
        // Filtre par période
        $today = Carbon::today();
        switch ($periode) {
            case 'aujourd\'hui':
                $query->whereDate('date_prevue', $today);
                break;
            
            case 'semaine':
                $endOfWeek = $today->copy()->addDays(7);
                $query->whereBetween('date_prevue', [$today, $endOfWeek]);
                break;
            
            case 'mois':
                $endOfMonth = $today->copy()->endOfMonth();
                $query->whereBetween('date_prevue', [$today, $endOfMonth]);
                break;
            
            case 'tous':
                // Aucun filtre supplémentaire
                break;
        }
        
        $rappels = $query->orderBy('date_prevue')
                         ->orderBy('heure_prevue')
                         ->paginate(20);
        
        return view('seances.rappels.index', compact('rappels', 'periode', 'search'));
    }

    /**
     * Marquer un rappel comme confirmé
     *
     * @param RappelRendezVous $rappel
     * @return \Illuminate\Http\Response
     */
    public function confirmer(RappelRendezVous $rappel)
    {
        $rappel->confirme = true;
        $rappel->statut = 'confirme';
        $rappel->save();
        
        return redirect()->route('rappels.index')
                         ->with('success', 'Le rendez-vous a été confirmé.');
    }

    /**
     * Marquer un rappel comme annulé
     *
     * @param RappelRendezVous $rappel
     * @return \Illuminate\Http\Response
     */
    public function annuler(RappelRendezVous $rappel)
    {
        $rappel->statut = 'annule';
        $rappel->save();
        
        return redirect()->route('rappels.index')
                         ->with('success', 'Le rendez-vous a été annulé.');
    }

    /**
     * Créer une séance à partir d'un rappel
     *
     * @param RappelRendezVous $rappel
     * @return \Illuminate\Http\Response
     */
    public function creerSeance(RappelRendezVous $rappel)
    {
        // Récupérer les informations de la séance précédente si disponible
        $ancienneSeance = $rappel->seance;
        
        // Rediriger vers le formulaire de création de séance avec les données préremplies
        return redirect()->route('seances.create', [
            'client_id' => $rappel->client_id,
            'date' => $rappel->date_prevue->format('Y-m-d'),
            'heure' => $rappel->heure_prevue->format('H:i'),
            'rappel_id' => $rappel->id,
            // Si une séance précédente existe, pré-remplir avec ses valeurs
            'prestation_id' => $ancienneSeance ? $ancienneSeance->prestation_id : null,
            'salon_id' => $ancienneSeance ? $ancienneSeance->salon_id : null
        ]);
    }
    
    /**
     * Créer un rappel de rendez-vous à partir d'une séance
     * 
     * @param Seance $seance
     * @param int $jours Nombre de jours dans le futur pour le prochain rendez-vous
     * @return bool
     */
    public static function creerRappelDepuisSeance(Seance $seance, $jours = 14)
    {
        try {
            DB::beginTransaction();
            
            // Récupérer la date actuelle et ajouter le nombre de jours indiqué
            $datePrevue = Carbon::now()->addDays($jours);
            $heurePrevue = Carbon::parse($seance->heure_prevu)->format('H:i:s');
            
            // Créer le rappel de rendez-vous
            $rappel = new RappelRendezVous([
                'client_id' => $seance->client_id,
                'seance_id' => $seance->id,
                'date_prevue' => $datePrevue,
                'heure_prevue' => $heurePrevue,
                'commentaire' => "Rappel automatique généré à partir de la séance du " . now()->format('d/m/Y'),
            ]);
            
            $rappel->save();
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
