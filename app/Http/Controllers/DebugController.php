<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    /**
     * Affiche les informations de débogage pour les anniversaires
     */
    public function debug()
    {
        // Requête identique à celle du ViewComposer
        $clientsAnniversaires = Client::whereNotNull('date_naissance')
            ->whereMonth('date_naissance', Carbon::now()->month)
            ->whereDay('date_naissance', Carbon::now()->day)
            ->get();
        
        // Détails des clients concernés
        $details = [];
        foreach ($clientsAnniversaires as $client) {
            $details[] = [
                'id' => $client->id,
                'nom' => $client->nom_complet,
                'telephone' => $client->numero_telephone,
                'date_naissance' => $client->date_naissance->format('Y-m-d'),
                'jour' => $client->date_naissance->format('d'),
                'mois' => $client->date_naissance->format('m'),
                'isAnniversaireToday' => $client->isAnniversaireToday(),
                'format_bd' => $client->date_naissance->format('d-m'),
                'format_now' => now()->format('d-m')
            ];
        }
        
        $dataCourante = [
            'date_aujourdhui' => Carbon::now()->format('Y-m-d'),
            'jour_aujourdhui' => Carbon::now()->day,
            'mois_aujourdhui' => Carbon::now()->month
        ];
        
        return response()->json([
            'nombre_clients' => $clientsAnniversaires->count(),
            'clients' => $details,
            'date_actuelle' => $dataCourante
        ]);
    }
}
