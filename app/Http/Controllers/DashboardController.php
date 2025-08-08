<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Salon;
use App\Models\Seance;
use App\Models\Prestation;
use App\Models\Reservation;
use App\Models\Product;
use App\Models\Purchase;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Statistiques de la journée
        $today = Carbon::today();
        
        // Séances du jour
        $seancesAujourdhui = Seance::whereDate('created_at', $today)->count();
        
        // Séances payantes et gratuites du jour
        $seancesPayantes = Seance::whereDate('created_at', $today)->where('is_free', false)->count();
        $seancesGratuites = Seance::whereDate('created_at', $today)->where('is_free', true)->count();
        
        // Revenus du jour
        $revenuTotal = Seance::whereDate('created_at', $today)->sum('prix');
        $revenuPayant = Seance::whereDate('created_at', $today)->where('is_free', false)->sum('prix');
        
        // Statistiques globales
        $totalClients = Client::count();
        $totalSalons = Salon::count();
        $totalPrestations = Prestation::count();
        $totalReservations = Reservation::count();
        $totalSeances = Seance::count();
        
        // Statistiques des produits
        $totalProducts = Product::count();
        $totalPurchases = Purchase::count();
        $lowStockProducts = Product::where(function($query) {
            $query->whereColumn('stock', '<=', 'alert_threshold')
                  ->orWhere('stock', '<=', 5);
        })->count();
        
        // Récupérer les 5 derniers produits en stock bas pour affichage dans le tableau
        $recentLowStockProducts = Product::where(function($query) {
            $query->whereColumn('stock', '<=', 'alert_threshold')
                  ->orWhere('stock', '<=', 5);
        })->with('category')->take(5)->get();
        
        return view('dashboard.index', compact(
            'seancesAujourdhui',
            'seancesPayantes',
            'seancesGratuites',
            'revenuTotal',
            'revenuPayant',
            'totalClients',
            'totalSalons',
            'totalPrestations',
            'totalReservations',
            'totalSeances',
            'totalProducts',
            'totalPurchases',
            'lowStockProducts',
            'recentLowStockProducts'
        ));
    }
}
