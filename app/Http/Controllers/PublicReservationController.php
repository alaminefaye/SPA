<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Prestation;
use App\Models\Salon;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicReservationController extends Controller
{
    /**
     * Affiche le formulaire de réservation publique
     */
    public function showForm()
    {
        $salons = Salon::orderBy('nom')->get();
        $prestations = Prestation::orderBy('nom_prestation')->get();
        
        return view('reservations.public.form', compact('salons', 'prestations'));
    }
    
    /**
     * Traite la soumission du formulaire de réservation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_complet' => 'required|string|max:255',
            'numero_telephone' => 'required|string|max:255',
            'adresse_mail' => 'required|email|max:255',
            'salon_id' => 'required|exists:salons,id',
            'prestation_id' => 'required|exists:prestations,id',
            'date_heure' => 'required|date|after:now',
            'commentaire' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Création ou mise à jour du client
        $client = Client::firstOrCreate(
            ['numero_telephone' => $request->numero_telephone],
            [
                'nom_complet' => $request->nom_complet,
                'adresse_mail' => $request->adresse_mail,
            ]
        );
        
        // Récupération des détails de la prestation
        $prestation = Prestation::findOrFail($request->prestation_id);
        
        // Création de la réservation
        Reservation::create([
            'client_id' => $client->id,
            'salon_id' => $request->salon_id,
            'prestation_id' => $request->prestation_id,
            'prix' => $prestation->prix,
            'duree' => $prestation->duree,
            'date_heure' => $request->date_heure,
            'statut' => 'en_attente', // Par défaut, une réservation client est en attente de confirmation
            'commentaire' => $request->commentaire,
            'client_created' => true, // Indique que cette réservation a été créée par un client
        ]);
        
        return redirect()->route('reservations.public.confirmation');
    }
    
    /**
     * Affiche la page de confirmation après une réservation réussie
     */
    public function confirmation()
    {
        return view('reservations.public.confirmation');
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
        
        return response()->json([
            'success' => true,
            'prestation' => [
                'id' => $prestation->id,
                'nom_prestation' => $prestation->nom_prestation,
                'prix' => $prestation->prix,
                'duree' => $prestation->duree ? $prestation->duree->format('H:i') : '00:00'
            ]
        ]);
    }
}
