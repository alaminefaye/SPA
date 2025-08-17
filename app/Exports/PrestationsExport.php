<?php

namespace App\Exports;

use App\Models\Prestation;
use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PrestationsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
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
        // Récupérer les séances avec leurs prestations pour la période spécifiée
        $seances = Seance::with('prestations')
            ->whereBetween('date_seance', [$this->startDate->format('Y-m-d'), $this->endDate->format('Y-m-d')])
            ->where('statut', 'terminee')
            ->get();
        
        $prestations = new Collection();
        
        foreach ($seances as $seance) {
            foreach ($seance->prestations as $prestation) {
                // Ajouter des informations supplémentaires à chaque prestation
                $prestation->date_seance = $seance->date_seance;
                $prestation->client_name = $seance->client->nom_complet;
                $prestation->salon_name = $seance->salon->nom;
                $prestation->seance_prix = $seance->prix;
                $prestation->is_free = $seance->is_free;
                
                $prestations->push($prestation);
            }
        }
        
        return $prestations;
    }
    
    /**
     * @var Prestation $prestation
     */
    public function map($prestation): array
    {
        // Convertir la durée au format lisible
        $dureeFormatted = Carbon::parse($prestation->duree)->format('H:i');
        
        return [
            Carbon::parse($prestation->date_seance)->format('d/m/Y'),
            $prestation->client_name,
            $prestation->salon_name,
            $prestation->nom_prestation,
            number_format($prestation->prix, 0, ',', ' ') . ' FCFA',
            $dureeFormatted,
            $prestation->is_free ? 'Oui' : 'Non',
        ];
    }
    
    public function headings(): array
    {
        return [
            'Date',
            'Client',
            'Salon',
            'Prestation',
            'Prix',
            'Durée',
            'Séance Gratuite',
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
        
        return "Rapport $type des Prestations ($dateRange)";
    }
}
