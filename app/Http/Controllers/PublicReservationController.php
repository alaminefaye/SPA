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
            'prestations' => 'required|array',
            'prestations.*' => 'exists:prestations,id',
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
        
        // Calculer le prix total et la durée totale
        $prestations = Prestation::whereIn('id', $request->prestations)->get();
        $prix_total = $prestations->sum('prix');
        
        // Convertir les durées en minutes, les additionner, puis reformater en H:i
        $duree_totale_minutes = 0;
        foreach ($prestations as $prestation) {
            // Convertir la durée au format H:i:s en minutes
            $time_parts = explode(':', $prestation->duree->format('H:i:s'));
            $duree_minutes = $time_parts[0] * 60 + $time_parts[1];
            $duree_totale_minutes += $duree_minutes;
        }
        
        // Convertir les minutes totales en format de durée
        $heures = floor($duree_totale_minutes / 60);
        $minutes = $duree_totale_minutes % 60;
        $duree_totale = sprintf('%02d:%02d', $heures, $minutes);
        
        // Création de la réservation
        $reservation = Reservation::create([
            'client_id' => $client->id,
            'salon_id' => $request->salon_id,
            'prix' => $prix_total,
            'duree' => $duree_totale,
            'date_heure' => $request->date_heure,
            'statut' => 'en_attente', // Par défaut, une réservation client est en attente de confirmation
            'commentaire' => $request->commentaire,
            'client_created' => true, // Indique que cette réservation a été créée par un client
        ]);
        
        // Associer les prestations à la réservation
        $reservation->prestations()->attach($request->prestations);
        
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
