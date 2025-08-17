<?php

namespace App\Exports;

use App\Models\Purchase;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, WithTitle
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
        return Purchase::with(['client'])
            ->whereBetween('created_at', [
                $this->startDate->startOfDay(),
                $this->endDate->endOfDay()
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function map($purchase): array
    {
        $productCount = $purchase->products->count();
        $productDetails = $purchase->products->map(function($product) {
            $quantity = $product->pivot->quantity;
            return "$product->name (x$quantity)";
        })->implode(', ');
        
        return [
            Carbon::parse($purchase->created_at)->format('d/m/Y H:i'),
            $purchase->client ? $purchase->client->nom_complet : 'Client anonyme',
            $productCount,
            $productDetails,
            number_format($purchase->total_amount, 0, ',', ' ') . ' FCFA',
            $purchase->payment_method,
            $purchase->status,
            $purchase->notes ?: '-',
        ];
    }
    
    public function headings(): array
    {
        return [
            'Date & Heure',
            'Client',
            'Nombre de produits',
            'Détails des produits',
            'Montant total',
            'Mode de paiement',
            'Statut',
            'Notes',
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
        
        return "Rapport $type des Achats ($dateRange)";
    }
}
