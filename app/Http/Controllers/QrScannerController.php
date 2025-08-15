<?php

namespace App\Http\Controllers;

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

        // Ici vous pouvez ajouter la logique pour traiter les données du QR code
        // Par exemple, rechercher un client, une séance, etc.

        return response()->json([
            'success' => true,
            'message' => 'QR code traité avec succès',
            'data' => $data['qr_data']
        ]);
    }
}
