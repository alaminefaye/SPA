<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientTemplateExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Exemples de clients
        return [
            [
                'Exemple Client 1', '0102030405', 'client1@example.com', '1990-01-15', 0
            ],
            [
                'Exemple Client 2', '0605040302', 'client2@example.com', '1985-06-22', 2
            ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'nom_complet',
            'numero_telephone',
            'adresse_mail',
            'date_naissance',
            'points',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Mettre les titres en gras et en fond gris clair
        $sheet->getStyle('A1:E1')->applyFromArray([
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

        // Ajouter des commentaires pour chaque colonne
        $sheet->getComment('A1')->getText()->createTextRun('Nom complet du client (obligatoire)');
        $sheet->getComment('B1')->getText()->createTextRun('Numéro de téléphone (obligatoire)');
        $sheet->getComment('C1')->getText()->createTextRun('Adresse email (optionnel, doit être unique si fourni)');
        $sheet->getComment('D1')->getText()->createTextRun('Date de naissance au format AAAA-MM-JJ (optionnel)');
        $sheet->getComment('E1')->getText()->createTextRun('Points de fidélité (optionnel, valeur numérique)');

        // Ajuster la largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);

        return [];
    }
}
