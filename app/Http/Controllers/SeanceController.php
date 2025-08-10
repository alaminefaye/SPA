<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Prestation;
use App\Models\Salon;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Seance::with(['client', 'salon', 'prestations']);
        
        if ($search) {
            $query->whereHas('client', function($q) use ($search) {
                $q->where('nom_complet', 'LIKE', "%{$search}%")
                  ->orWhere('numero_telephone', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('salon', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('prestations', function($q) use ($search) {
                $q->where('nom_prestation', 'LIKE', "%{$search}%");
            });
        }
        
        $seances = $query->orderBy('created_at', 'desc')
                         ->paginate(10)
                         ->withQueryString();
        
        return view('seances.index', compact('seances', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $salons = Salon::orderBy('nom')->get();
        $prestations = Prestation::orderBy('nom_prestation')->get();
        
        return view('seances.create', compact('salons', 'prestations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_telephone' => 'required|string|max:255',
            'salon_id' => 'required|exists:salons,id',
            'prestations' => 'required|array',
            'prestations.*' => 'exists:prestations,id',
            'commentaire' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Vérification ou création du client
        if ($request->filled('nom_complet') && $request->filled('adresse_mail')) {
            // Création d'un nouveau client si tous les champs sont remplis
            $client = Client::firstOrCreate(
                ['numero_telephone' => $request->numero_telephone],
                [
                    'nom_complet' => $request->nom_complet,
                    'adresse_mail' => $request->adresse_mail,
                ]
            );
        } else {
            // Vérification de l'existence du client avec ce numéro de téléphone
            $client = Client::where('numero_telephone', $request->numero_telephone)->first();
            
            if (!$client) {
                return redirect()->back()
                    ->withErrors(['numero_telephone' => 'Aucun client trouvé avec ce numéro de téléphone. Veuillez fournir toutes les informations du client.'])
                    ->withInput();
            }
        }
        
        // Création de la séance
        $seance = new Seance();
        $seance->client_id = $client->id;
        $seance->salon_id = $request->salon_id;
        $seance->statut = 'planifiee';
        $seance->commentaire = $request->commentaire;
        
        // Définir automatiquement la date du jour pour la séance
        $seance->date_seance = now()->toDateString();
        
        // Si l'heure prévue est fournie, l'utiliser, sinon mettre l'heure actuelle
        if ($request->filled('heure_prevu')) {
            $seance->heure_prevu = $request->heure_prevu;
        } else {
            $seance->heure_prevu = now()->format('H:i:s');
        }
        
        // Initialiser le prix à 0 et la durée à un format valide pour éviter l'erreur NOT NULL
        $seance->prix = 0;
        $seance->duree = '00:00:00';
        
        // Sauvegarde initiale de la séance pour obtenir un ID
        $seance->save();
        
        // Ajout des prestations sélectionnées
        if ($request->has('prestations') && is_array($request->prestations)) {
            foreach ($request->prestations as $prestationId) {
                $seance->prestations()->attach($prestationId);
            }
        }
        
        // Calcul et mise à jour du prix et de la durée totale
        $seance->prix = $seance->calculerPrixTotal();
        $seance->duree = $seance->calculerDureeTotale();
        
        // Vérifier si c'est une séance gratuite (utilisation des points de fidélité)
        $pointsNecessaires = 5; // Points nécessaires pour une séance gratuite
        
        if ($request->has('utiliser_points') && $request->utiliser_points) {
            if ($client->peutObtenirSeanceGratuite($pointsNecessaires)) {
                $client->utiliserPoints($pointsNecessaires);
                $seance->is_free = true;
                $seance->prix = 0; // La séance est gratuite, donc prix à 0
            }
        } else {
            // Si ce n'est pas une séance gratuite, ajouter un point de fidélité
            $client->ajouterPoints(1);
        }
        
        $seance->save();
        
        return redirect()->route('seances.index')
            ->with('success', 'Séance créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seance = Seance::with(['client', 'salon', 'prestations'])->findOrFail($id);
        return view('seances.show', compact('seance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $seance = Seance::findOrFail($id);
        $salons = Salon::orderBy('nom')->get();
        $prestations = Prestation::orderBy('nom_prestation')->get();
        
        return view('seances.edit', compact('seance', 'salons', 'prestations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'salon_id' => 'required|exists:salons,id',
            'prestations' => 'required|array',
            'prestations.*' => 'exists:prestations,id',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'commentaire' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $seance = Seance::findOrFail($id);
        
        // Mise à jour des données de base de la séance
        $seance->salon_id = $request->salon_id;
        $seance->statut = $request->statut;
        $seance->commentaire = $request->commentaire;
        
        // Mise à jour des prestations
        $seance->prestations()->detach(); // Supprime les anciennes relations
        
        if ($request->has('prestations') && is_array($request->prestations)) {
            $seance->prestations()->sync($request->prestations);
        }
        
        // Recalcul du prix et de la durée totale
        $seance->prix = $seance->calculerPrixTotal();
        $seance->duree = $seance->calculerDureeTotale();
        $seance->save();
        
        return redirect()->route('seances.index')
            ->with('success', 'Séance mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $seance = Seance::findOrFail($id);
        $seance->delete();
        
        return redirect()->route('seances.index')
            ->with('success', 'Séance supprimée avec succès');
    }
    
    /**
     * Get client information by phone number (AJAX).
     */
    public function getClientByPhone(Request $request)
    {
        $phone = $request->input('phone');
        $client = Client::where('numero_telephone', $phone)->first();
        
        if ($client) {
            return response()->json([
                'success' => true,
                'client' => $client
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Aucun client trouvé avec ce numéro de téléphone'
            ]);
        }
    }
    
    /**
     * Get prestation details (AJAX).
     */
    public function getPrestationDetails(Request $request)
    {
        $prestation_id = $request->input('prestation_id');
        
        if (!$prestation_id) {
            return response()->json([
                'success' => false,
                'message' => 'ID de prestation manquant'
            ]);
        }
        
        $prestation = Prestation::find($prestation_id);
        
        if (!$prestation) {
            return response()->json([
                'success' => false,
                'message' => 'Prestation non trouvée'
            ]);
        }
        
        // Assurons-nous que les données sont bien formatées
        return response()->json([
            'success' => true,
            'prestation' => [
                'id' => $prestation->id,
                'nom_prestation' => $prestation->nom_prestation,
                'prix' => $prestation->prix,
                'duree' => $prestation->duree ? $prestation->duree->format('H:i:s') : '00:00:00'
            ]
        ]);
    }
    
    /**
     * Démarre une séance en modifiant son statut à 'en_cours'
     */
    public function demarrer(string $id)
    {
        $seance = Seance::findOrFail($id);
        $seance->statut = 'en_cours';
        $seance->heure_debut = now();
        $seance->save();
        
        // Calcul de la durée totale en minutes pour le timer JavaScript
        $dureeMinutes = 0;
        foreach ($seance->prestations as $prestation) {
            $dureeParts = explode(':', $prestation->duree->format('H:i:s'));
            $dureeMinutes += $dureeParts[0] * 60 + $dureeParts[1];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Séance démarrée avec succès',
            'duree_minutes' => $dureeMinutes,
            'duree_formatee' => $seance->duree ? $seance->duree->format('H:i:s') : '00:00:00'
        ]);
    }
    
    /**
     * Termine une séance en modifiant son statut à 'termine'
     */
    public function terminer(string $id)
    {
        $seance = Seance::findOrFail($id);
        $seance->statut = 'termine';
        $seance->heure_fin = now();
        $seance->save();
        
        // Si la requête est AJAX (comme celle du bouton "Terminer"), retourner JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Séance terminée avec succès'
            ]);
        }
        
        // Sinon, rediriger avec un message flash
        return redirect()->route('seances.show', $id)
            ->with('success', 'Séance terminée avec succès');
    }
    
    /**
     * Affiche les séances du jour qui ne sont pas encore démarrées
     */
    public function aDemarrer()
    {
        $today = now()->toDateString(); // Format Y-m-d
        
        $seances = Seance::with(['client', 'salon', 'prestations'])
            ->whereDate('date_seance', $today)
            ->whereIn('statut', ['planifiee', 'en_cours']) // Afficher les séances planifiées ET en cours
            ->orderBy('heure_prevu', 'asc')
            ->get();
        
        return view('seances.a_demarrer', compact('seances'));
    }
    
    /**
     * API pour récupérer les séances en cours (pour les notifications)
     */
    public function getSeancesEnCours()
    {
        $seances = Seance::with(['client', 'prestations'])
            ->where('statut', 'en_cours')
            ->whereNotNull('heure_debut')
            ->get()
            ->map(function($seance) {
                // Calcul de la durée totale en minutes pour le timer JavaScript
                $dureeMinutes = 0;
                foreach ($seance->prestations as $prestation) {
                    $dureeParts = explode(':', $prestation->duree->format('H:i:s'));
                    $dureeMinutes += $dureeParts[0] * 60 + $dureeParts[1];
                }
                
                return [
                    'id' => $seance->id,
                    'heure_debut' => $seance->heure_debut,
                    'client_nom' => $seance->client->nom_complet,
                    'statut' => $seance->statut,
                    'duree_minutes' => $dureeMinutes
                ];
            });
            
        return response()->json([
            'success' => true,
            'seances' => $seances
        ]);
    }
    
    /**
     * Affiche la page de test des notifications
     * 
     * @return \Illuminate\View\View
     */
    public function notificationTest()
    {
        return view('seances.notification-test');
    }
    
    /**
     * API pour vérifier si une séance est toujours active (non terminée)
     *
     * @param string $id ID de la séance
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSeanceStatus(string $id)
    {
        $seance = Seance::findOrFail($id);
        
        // Une séance est considérée comme active si elle n'est pas terminée
        $isActive = $seance->statut !== 'termine';
        
        return response()->json([
            'success' => true,
            'active' => $isActive,
            'statut' => $seance->statut
        ]);
    }
    
    /**
     * Affiche la liste des séances terminées
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function terminees(Request $request)
    {
        $search = $request->input('search');
        
        $query = Seance::with(['client', 'salon', 'prestations'])
            ->where('statut', 'termine')
            ->whereNotNull('heure_debut')
            ->whereNotNull('heure_fin');
        
        if ($search) {
            $query->whereHas('client', function($q) use ($search) {
                $q->where('nom_complet', 'LIKE', "%{$search}%")
                  ->orWhere('numero_telephone', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('salon', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('prestations', function($q) use ($search) {
                $q->where('nom_prestation', 'LIKE', "%{$search}%");
            });
        }
        
        $seances = $query->orderBy('heure_fin', 'desc')
                         ->paginate(10)
                         ->withQueryString();
        
        return view('seances.terminees', compact('seances', 'search'));
    }
}
