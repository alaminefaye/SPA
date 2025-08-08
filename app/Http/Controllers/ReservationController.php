<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Prestation;
use App\Models\Salon;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Reservation::with(['client', 'salon', 'prestations']);
        
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
        
        $reservations = $query->orderBy('date_heure', 'desc')
                          ->paginate(10)
                          ->withQueryString();
        
        return view('reservations.admin.index', compact('reservations', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $salons = Salon::orderBy('nom')->get();
        $prestations = Prestation::orderBy('nom_prestation')->get();
        
        return view('reservations.admin.create', compact('salons', 'prestations'));
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
            'prix' => 'required|numeric',
            'duree' => 'required',
            'date_heure' => 'required|date',
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
        
        // Création de la réservation
        $reservation = Reservation::create([
            'client_id' => $client->id,
            'salon_id' => $request->salon_id,
            'prix' => $request->prix,
            'duree' => $request->duree,
            'date_heure' => $request->date_heure,
            'statut' => 'confirme', // Par défaut, une réservation créée par l'admin est confirmée
            'commentaire' => $request->commentaire,
            'client_created' => false, // Indique que cette réservation a été créée par l'administrateur
        ]);
        
        // Associer les prestations à la réservation
        $reservation->prestations()->attach($request->prestations);
        
        return redirect()->route('reservations.index')
            ->with('success', 'Réservation créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['client', 'salon', 'prestations'])->findOrFail($id);
        
        // Marquer cette réservation comme lue si elle ne l'était pas déjà
        if (!$reservation->is_read) {
            $reservation->is_read = true;
            $reservation->save();
        }
        
        return view('reservations.admin.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        $salons = Salon::orderBy('nom')->get();
        $prestations = Prestation::orderBy('nom_prestation')->get();
        
        return view('reservations.admin.edit', compact('reservation', 'salons', 'prestations'));
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
            'prix' => 'required|numeric',
            'duree' => 'required',
            'date_heure' => 'required|date',
            'statut' => 'required|in:en_attente,confirme,en_cours,termine,annule',
            'commentaire' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $reservation = Reservation::findOrFail($id);
        
        // Mise à jour des informations de la réservation
        $reservation->update([
            'salon_id' => $request->salon_id,
            'prix' => $request->prix,
            'duree' => $request->duree,
            'date_heure' => $request->date_heure,
            'statut' => $request->statut,
            'commentaire' => $request->commentaire,
        ]);
        
        // Synchroniser les prestations
        $reservation->prestations()->sync($request->prestations);
        
        return redirect()->route('reservations.index')
            ->with('success', 'Réservation mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        
        return redirect()->route('reservations.index')
            ->with('success', 'Réservation supprimée avec succès');
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
        $prestation_ids = $request->input('prestations');
        
        if (!$prestation_ids || !is_array($prestation_ids) || empty($prestation_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Au moins une prestation est requise'
            ]);
        }
        
        // Calculer le prix total et la durée totale de toutes les prestations sélectionnées
        $prestations = Prestation::whereIn('id', $prestation_ids)->get();
        $prix_total = $prestations->sum('prix');
        
        // Convertir les durées en minutes, les additionner, puis reformater en H:i
        $duree_totale_minutes = 0;
        foreach ($prestations as $prestation) {
            // Convertir la durée au format H:i:s en minutes
            $time_parts = explode(':', $prestation->duree->format('H:i:s'));
            $duree_minutes = $time_parts[0] * 60 + $time_parts[1];
            $duree_totale_minutes += $duree_minutes;
        }
        
        // Convertir les minutes totales en heures et minutes
        $heures = floor($duree_totale_minutes / 60);
        $minutes = $duree_totale_minutes % 60;
        $duree_totale = sprintf('%02d:%02d', $heures, $minutes);
        
        return response()->json([
            'success' => true,
            'prix' => $prix_total,
            'duree' => $duree_totale
        ]);
    }
}
