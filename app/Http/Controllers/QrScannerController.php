<?php

namespace App\Http\Controllers;

use App\Models\Seance;
use Illuminate\Http\Request;

class QrScannerController extends Controller
{
    /**
     * Affiche la page du scanner de QR code
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('qrscanner.index');
    }

    /**
     * Traite les données d'un QR code scanné
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(Request $request)
    {
        $data = $request->validate([
            'qr_data' => 'required|string',
        ]);

        $qrData = $data['qr_data'];

        // Vérifier si les données du QR sont au format attendu pour une séance
        if (strpos($qrData, 'seance:') === 0) {
            // Extraire l'ID de la séance
            $seanceId = str_replace('seance:', '', $qrData);
            
            // Vérifier si la séance existe
            $seance = Seance::find($seanceId);
            
            if ($seance) {
                // Si la séance est planifiée, la démarrer
                if ($seance->statut === Seance::STATUT_PLANIFIEE) {
                    $seance->statut = Seance::STATUT_EN_COURS;
                    $seance->date_debut = now();
                    $seance->save();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Séance démarrée avec succès',
                        'redirect' => route('seances.demarrer', $seance->id),
                        'data' => [
                            'id' => $seance->id,
                            'numero' => $seance->numero_seance,
                            'client' => $seance->client->nom_complet,
                            'statut' => $seance->statut
                        ]
                    ]);
                }
                
                // Si la séance est déjà en cours ou terminée
                return response()->json([
                    'success' => true,
                    'message' => 'Séance trouvée',
                    'redirect' => route('seances.show', $seance->id),
                    'data' => [
                        'id' => $seance->id,
                        'numero' => $seance->numero_seance,
                        'client' => $seance->client->nom_complet,
                        'statut' => $seance->statut
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Séance non trouvée',
                'data' => $qrData
            ]);
        }
        
        // Si ce n'est pas une séance, retourner les données brutes
        return response()->json([
            'success' => true,
            'message' => 'QR code traité avec succès',
            'data' => $qrData
        ]);
    }
}
