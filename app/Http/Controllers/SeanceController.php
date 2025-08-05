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
        
        $query = Seance::with(['client', 'salon', 'prestation']);
        
        if ($search) {
            $query->whereHas('client', function($q) use ($search) {
                $q->where('nom_complet', 'LIKE', "%{$search}%")
                  ->orWhere('numero_telephone', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('salon', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('prestation', function($q) use ($search) {
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
            'prestation_id' => 'required|exists:prestations,id',
            'prix' => 'required|numeric',
            'duree' => 'required',
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
        Seance::create([
            'client_id' => $client->id,
            'salon_id' => $request->salon_id,
            'prestation_id' => $request->prestation_id,
            'prix' => $request->prix,
            'duree' => $request->duree,
            'statut' => 'planifie',
            'commentaire' => $request->commentaire,
        ]);
        
        return redirect()->route('seances.index')
            ->with('success', 'Séance créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seance = Seance::with(['client', 'salon', 'prestation'])->findOrFail($id);
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
            'prestation_id' => 'required|exists:prestations,id',
            'prix' => 'required|numeric',
            'duree' => 'required',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'commentaire' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $seance = Seance::findOrFail($id);
        $seance->update($request->all());
        
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
}
