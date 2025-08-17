<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PrestationsExport;
use App\Exports\ProductsExport;
use App\Exports\PurchasesExport;
use App\Http\Controllers\Controller;
use App\Models\Prestation;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la page d'index des rapports
     */
    public function index()
    {
        return view('admin.reports.index');
    }
    
    /**
     * Affiche et génère le rapport pour les prestations
     */
    public function prestations(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        // Ajuster la période selon le type de rapport
        switch($reportType) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'annual':
                $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Utiliser les dates fournies par l'utilisateur
                break;
            default: // daily
                $startDate = Carbon::today()->format('Y-m-d');
                $endDate = Carbon::today()->format('Y-m-d');
                break;
        }
        
        // Récupérer les données pour l'affichage dans la vue
        $seances = Seance::with(['client', 'prestations', 'salon'])
            ->whereBetween('date_seance', [$startDate, $endDate])
            ->where('statut', 'terminee')
            ->orderBy('date_seance', 'desc')
            ->get();
            
        // Calculer les statistiques
        $totalPrestations = 0;
        $totalRevenue = 0;
        $prestationsCount = [];
        
        foreach ($seances as $seance) {
            $totalRevenue += $seance->is_free ? 0 : $seance->prix;
            $totalPrestations += $seance->prestations->count();
            
            foreach ($seance->prestations as $prestation) {
                if (!isset($prestationsCount[$prestation->id])) {
                    $prestationsCount[$prestation->id] = [
                        'nom' => $prestation->nom_prestation,
                        'count' => 0,
                        'revenue' => 0
                    ];
                }
                
                $prestationsCount[$prestation->id]['count'] += 1;
                $prestationsCount[$prestation->id]['revenue'] += $seance->is_free ? 0 : $prestation->prix;
            }
        }
        
        // Trier les prestations par popularité
        uasort($prestationsCount, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        
        return view('admin.reports.prestations', compact(
            'seances',
            'startDate',
            'endDate',
            'reportType',
            'totalPrestations',
            'totalRevenue',
            'prestationsCount'
        ));
    }
    
    /**
     * Affiche et génère le rapport pour les produits vendus
     */
    public function products(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        // Ajuster la période selon le type de rapport
        switch($reportType) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'annual':
                $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Utiliser les dates fournies par l'utilisateur
                break;
            default: // daily
                $startDate = Carbon::today()->format('Y-m-d');
                $endDate = Carbon::today()->format('Y-m-d');
                break;
        }
        
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();
        
        // Récupérer les achats pour la période
        $purchases = Purchase::with(['products', 'client'])
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques sur les produits vendus
        $productStats = [];
        $totalSales = 0;
        $totalRevenue = 0;
        $totalProducts = 0;
        
        foreach ($purchases as $purchase) {
            $totalSales += 1;
            $totalRevenue += $purchase->total_amount;
            
            foreach ($purchase->products as $product) {
                $quantity = $product->pivot->quantity;
                $totalProducts += $quantity;
                
                if (!isset($productStats[$product->id])) {
                    $productStats[$product->id] = [
                        'name' => $product->name,
                        'quantity' => 0,
                        'revenue' => 0
                    ];
                }
                
                $productStats[$product->id]['quantity'] += $quantity;
                $productStats[$product->id]['revenue'] += $product->price * $quantity;
            }
        }
        
        // Trier les produits par quantité vendue
        uasort($productStats, function ($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });
        
        return view('admin.reports.products', compact(
            'purchases',
            'startDate',
            'endDate',
            'reportType',
            'productStats',
            'totalSales',
            'totalRevenue',
            'totalProducts'
        ));
    }
    
    /**
     * Export du rapport des prestations au format Excel
     */
    public function exportPrestationsExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filename = 'rapport-prestations-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new PrestationsExport($startDate, $endDate, $reportType), $filename);
    }
    
    /**
     * Export du rapport des prestations au format PDF
     */
    public function exportPrestationsPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        // Logique identique à celle de la méthode prestations()
        $seances = Seance::with(['client', 'prestations', 'salon'])
            ->whereBetween('date_seance', [$startDate, $endDate])
            ->where('statut', 'terminee')
            ->orderBy('date_seance', 'desc')
            ->get();
        
        $totalPrestations = 0;
        $totalRevenue = 0;
        $prestationsCount = [];
        
        foreach ($seances as $seance) {
            $totalRevenue += $seance->is_free ? 0 : $seance->prix;
            $totalPrestations += $seance->prestations->count();
            
            foreach ($seance->prestations as $prestation) {
                if (!isset($prestationsCount[$prestation->id])) {
                    $prestationsCount[$prestation->id] = [
                        'nom' => $prestation->nom_prestation,
                        'count' => 0,
                        'revenue' => 0
                    ];
                }
                
                $prestationsCount[$prestation->id]['count'] += 1;
                $prestationsCount[$prestation->id]['revenue'] += $seance->is_free ? 0 : $prestation->prix;
            }
        }
        
        // Trier les prestations par popularité
        uasort($prestationsCount, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        
        // Générer le PDF
        $reportTypes = [
            'daily' => 'Journalier',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'annual' => 'Annuel',
            'custom' => 'Personnalisé'
        ];
        
        $reportTitle = "Rapport " . ($reportTypes[$reportType] ?? 'Personnalisé') . " des Prestations";
        $dateRange = Carbon::parse($startDate)->format('d/m/Y');
        
        if ($startDate !== $endDate) {
            $dateRange .= ' au ' . Carbon::parse($endDate)->format('d/m/Y');
        }
        
        $pdf = PDF::loadView('admin.reports.prestations_pdf', compact(
            'seances', 
            'dateRange', 
            'reportTitle',
            'totalPrestations',
            'totalRevenue',
            'prestationsCount'
        ));
        
        return $pdf->download('rapport-prestations-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Export du rapport des produits vendus au format Excel
     */
    public function exportProductsExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filename = 'rapport-produits-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new ProductsExport($startDate, $endDate, $reportType), $filename);
    }
    
    /**
     * Export du rapport des produits vendus au format PDF
     */
    public function exportProductsPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();
        
        // Récupérer les achats pour la période
        $purchases = Purchase::with(['products', 'client'])
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques sur les produits vendus
        $productStats = [];
        $totalSales = 0;
        $totalRevenue = 0;
        $totalProducts = 0;
        
        foreach ($purchases as $purchase) {
            $totalSales += 1;
            $totalRevenue += $purchase->total_amount;
            
            foreach ($purchase->products as $product) {
                $quantity = $product->pivot->quantity;
                $totalProducts += $quantity;
                
                if (!isset($productStats[$product->id])) {
                    $productStats[$product->id] = [
                        'name' => $product->name,
                        'quantity' => 0,
                        'revenue' => 0
                    ];
                }
                
                $productStats[$product->id]['quantity'] += $quantity;
                $productStats[$product->id]['revenue'] += $product->price * $quantity;
            }
        }
        
        // Trier les produits par quantité vendue
        uasort($productStats, function ($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });
        
        // Générer le PDF
        $reportTypes = [
            'daily' => 'Journalier',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'annual' => 'Annuel',
            'custom' => 'Personnalisé'
        ];
        
        $reportTitle = "Rapport " . ($reportTypes[$reportType] ?? 'Personnalisé') . " des Ventes de Produits";
        $dateRange = Carbon::parse($startDate)->format('d/m/Y');
        
        if ($startDate !== $endDate) {
            $dateRange .= ' au ' . Carbon::parse($endDate)->format('d/m/Y');
        }
        
        $pdf = PDF::loadView('admin.reports.products_pdf', compact(
            'purchases', 
            'dateRange', 
            'reportTitle',
            'productStats',
            'totalSales',
            'totalRevenue',
            'totalProducts'
        ));
        
        return $pdf->download('rapport-produits-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Export du rapport des achats au format Excel
     */
    public function exportPurchasesExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filename = 'rapport-achats-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new PurchasesExport($startDate, $endDate, $reportType), $filename);
    }
    
    /**
     * Affiche et génère le rapport des séances
     */
    public function seances(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        // Ajuster la période selon le type de rapport
        switch($reportType) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'annual':
                $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Utiliser les dates fournies par l'utilisateur
                break;
            default: // daily
                $startDate = Carbon::today()->format('Y-m-d');
                $endDate = Carbon::today()->format('Y-m-d');
                break;
        }
        
        // Récupérer les séances pour la période demandée
        $seances = Seance::with(['client', 'prestations', 'salon'])
            ->whereBetween('date_seance', [$startDate, $endDate])
            ->orderBy('date_seance', 'desc')
            ->get();
        
        // Statistiques sur les séances
        $totalSeances = $seances->count();
        $totalTerminees = $seances->where('statut', 'terminee')->count();
        $totalAnnulees = $seances->where('statut', 'annulee')->count();
        $totalEnAttente = $seances->where('statut', 'en_attente')->count();
        $totalRevenu = $seances->where('statut', 'terminee')->where('is_free', false)->sum('prix');
        
        // Statistiques par salon
        $seancesBySalon = [];
        foreach ($seances as $seance) {
            if (!isset($seancesBySalon[$seance->salon->id])) {
                $seancesBySalon[$seance->salon->id] = [
                    'nom' => $seance->salon->nom,
                    'total' => 0,
                    'terminees' => 0,
                    'annulees' => 0,
                    'en_attente' => 0,
                    'revenu' => 0
                ];
            }
            
            $seancesBySalon[$seance->salon->id]['total']++;
            
            if ($seance->statut === 'terminee') {
                $seancesBySalon[$seance->salon->id]['terminees']++;
                if (!$seance->is_free) {
                    $seancesBySalon[$seance->salon->id]['revenu'] += $seance->prix;
                }
            } elseif ($seance->statut === 'annulee') {
                $seancesBySalon[$seance->salon->id]['annulees']++;
            } elseif ($seance->statut === 'en_attente') {
                $seancesBySalon[$seance->salon->id]['en_attente']++;
            }
        }
        
        return view('admin.reports.seances', compact(
            'seances',
            'startDate',
            'endDate',
            'reportType',
            'totalSeances',
            'totalTerminees',
            'totalAnnulees',
            'totalEnAttente',
            'totalRevenu',
            'seancesBySalon'
        ));
    }
    
    /**
     * Export du rapport des séances au format Excel
     */
    public function exportSeancesExcel(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filename = 'rapport-seances-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new SeancesExport($startDate, $endDate, $reportType), $filename);
    }
    
    /**
     * Export du rapport des séances au format PDF
     */
    public function exportSeancesPdf(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        // Récupérer les séances pour la période demandée
        $seances = Seance::with(['client', 'prestations', 'salon'])
            ->whereBetween('date_seance', [$startDate, $endDate])
            ->orderBy('date_seance', 'desc')
            ->get();
        
        // Statistiques sur les séances
        $totalSeances = $seances->count();
        $totalTerminees = $seances->where('statut', 'terminee')->count();
        $totalAnnulees = $seances->where('statut', 'annulee')->count();
        $totalEnAttente = $seances->where('statut', 'en_attente')->count();
        $totalRevenu = $seances->where('statut', 'terminee')->where('is_free', false)->sum('prix');
        
        // Statistiques par salon
        $seancesBySalon = [];
        foreach ($seances as $seance) {
            if (!isset($seancesBySalon[$seance->salon->id])) {
                $seancesBySalon[$seance->salon->id] = [
                    'nom' => $seance->salon->nom,
                    'total' => 0,
                    'terminees' => 0,
                    'annulees' => 0,
                    'en_attente' => 0,
                    'revenu' => 0
                ];
            }
            
            $seancesBySalon[$seance->salon->id]['total']++;
            
            if ($seance->statut === 'terminee') {
                $seancesBySalon[$seance->salon->id]['terminees']++;
                if (!$seance->is_free) {
                    $seancesBySalon[$seance->salon->id]['revenu'] += $seance->prix;
                }
            } elseif ($seance->statut === 'annulee') {
                $seancesBySalon[$seance->salon->id]['annulees']++;
            } elseif ($seance->statut === 'en_attente') {
                $seancesBySalon[$seance->salon->id]['en_attente']++;
            }
        }
        
        // Générer le PDF
        $reportTypes = [
            'daily' => 'Journalier',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'annual' => 'Annuel',
            'custom' => 'Personnalisé'
        ];
        
        $reportTitle = "Rapport " . ($reportTypes[$reportType] ?? 'Personnalisé') . " des Séances";
        $dateRange = Carbon::parse($startDate)->format('d/m/Y');
        
        if ($startDate !== $endDate) {
            $dateRange .= ' au ' . Carbon::parse($endDate)->format('d/m/Y');
        }
        
        $pdf = PDF::loadView('admin.reports.seances_pdf', compact(
            'seances', 
            'dateRange', 
            'reportTitle',
            'totalSeances',
            'totalTerminees',
            'totalAnnulees',
            'totalEnAttente',
            'totalRevenu',
            'seancesBySalon'
        ));
        
        return $pdf->download('rapport-seances-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}
