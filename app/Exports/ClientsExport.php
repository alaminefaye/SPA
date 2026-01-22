<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Client::orderBy('nom_complet', 'asc')->get();
    }

    /**
     * @var Client $client
     */
    public function map($client): array
    {
        return [
            $client->nom_complet,
            $client->numero_telephone,
            $client->adresse_mail ?? '',
            $client->date_naissance ? $client->date_naissance->format('Y-m-d') : '',
            $client->points ?? 0,
            $client->created_at ? $client->created_at->format('Y-m-d H:i:s') : '',
        ];
    }

    public function headings(): array
    {
        return [
            'Nom complet',
            'Numéro téléphone',
            'Adresse mail',
            'Date de naissance',
            'Points',
            'Date de création',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Mettre les titres en gras et en fond gris clair
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'EEEEEE',
                ],
            ],
        ]);

        return [];
    }
}
