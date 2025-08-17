<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $reportType;

    public function __construct($startDate = null, $endDate = null, $reportType = 'daily')
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : Carbon::today();
        $this->endDate = $endDate ? Carbon::parse($endDate) : Carbon::today();
        $this->reportType = $reportType;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Récupérer les achats avec leurs produits pour la période spécifiée
        $purchases = Purchase::with(['products', 'client'])
            ->whereBetween('created_at', [
                $this->startDate->startOfDay(),
                $this->endDate->endOfDay()
            ])
            ->where('status', 'completed') // Uniquement les achats terminés
            ->get();

        $soldProducts = new Collection();
        
        foreach ($purchases as $purchase) {
            foreach ($purchase->products as $product) {
                $pivot = $product->pivot;
                
                // Créer un nouvel objet pour chaque produit vendu
                $soldProduct = (object) [
                    'date' => $purchase->created_at,
                    'product_name' => $product->name,
                    'product_category' => $product->category ? $product->category->name : 'Sans catégorie',
                    'quantity' => $pivot->quantity,
                    'price' => $product->price,
                    'total' => $product->price * $pivot->quantity,
                    'client_name' => $purchase->client ? $purchase->client->nom_complet : 'Client anonyme',
                    'payment_method' => $purchase->payment_method
                ];
                
                $soldProducts->push($soldProduct);
            }
        }
        
        return $soldProducts;
    }

    public function map($product): array
    {
        return [
            Carbon::parse($product->date)->format('d/m/Y'),
            $product->product_name,
            $product->product_category,
            $product->quantity,
            number_format($product->price, 0, ',', ' ') . ' FCFA',
            number_format($product->total, 0, ',', ' ') . ' FCFA',
            $product->client_name,
            $product->payment_method,
        ];
    }
    
    public function headings(): array
    {
        return [
            'Date',
            'Produit',
            'Catégorie',
            'Quantité',
            'Prix unitaire',
            'Total',
            'Client',
            'Mode de paiement',
        ];
    }
    
    public function title(): string
    {
        $dateRange = $this->startDate->format('d/m/Y');
        if ($this->startDate->format('Y-m-d') !== $this->endDate->format('Y-m-d')) {
            $dateRange .= ' au ' . $this->endDate->format('d/m/Y');
        }
        
        $reportTypes = [
            'daily' => 'Journalier',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'annual' => 'Annuel',
            'custom' => 'Personnalisé'
        ];
        
        $type = $reportTypes[$this->reportType] ?? 'Personnalisé';
        
        return "Rapport $type des Ventes de Produits ($dateRange)";
    }
}
