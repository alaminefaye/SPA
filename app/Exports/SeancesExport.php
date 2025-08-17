<?php

namespace App\Exports;

use App\Models\Seance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SeancesExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $reportType;

    public function __construct($startDate = null, $endDate = null, $reportType = 'daily')
    {
        $this->startDate = $startDate ?? Carbon::today()->format('Y-m-d');
        $this->endDate = $endDate ?? Carbon::today()->format('Y-m-d');
        $this->reportType = $reportType;
        
        // Ajuster la période selon le type de rapport
        switch($this->reportType) {
            case 'weekly':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'monthly':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'annual':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Utiliser les dates fournies
                break;
            default: // daily
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
        }
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Seance::with(['client', 'prestations', 'salon'])
            ->whereBetween('date_seance', [$this->startDate, $this->endDate])
            ->orderBy('date_seance', 'desc')
            ->get();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $reportTypes = [
            'daily' => 'Journalier',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'annual' => 'Annuel',
            'custom' => 'Personnalisé'
        ];

        return 'Rapport ' . ($reportTypes[$this->reportType] ?? 'Personnalisé') . ' des Séances';
    }

    /**
     * @var Seance $seance
     */
    public function map($seance): array
    {
        $prestations = $seance->prestations->pluck('nom_prestation')->implode(', ');
        
        return [
            $seance->id,
            $seance->date_seance,
            $seance->heure_prevu,
            $seance->client ? $seance->client->nom_complet : 'Client supprimé',
            $seance->salon ? $seance->salon->nom : 'Salon supprimé',
            $prestations,
            $seance->prix . ' fr',
            $seance->duree,
            $seance->statut,
            $seance->is_free ? 'Oui' : 'Non',
            $seance->commentaire
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Date',
            'Heure',
            'Client',
            'Salon',
            'Prestations',
            'Prix',
            'Durée',
            'Statut',
            'Séance gratuite',
            'Commentaire'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
