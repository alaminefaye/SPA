<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ClientImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading, SkipsOnError
{
    use Importable, SkipsFailures, SkipsErrors;
    
    private $processedPhones = [];
    private $duplicatesCount = 0;
    
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Normaliser le numéro de téléphone (enlever les espaces)
        $normalizedPhone = str_replace(' ', '', $row['numero_telephone']);
        
        // Vérifier si le numéro de téléphone est déjà traité dans ce lot d'importation
        if (in_array($normalizedPhone, $this->processedPhones)) {
            $this->duplicatesCount++;
            return null; // Ignorer ce doublon
        }
        
        // Vérifier si le client existe déjà dans la base de données
        $existingClient = Client::where('numero_telephone', 'like', '%' . $normalizedPhone . '%')->first();
        if ($existingClient) {
            $this->duplicatesCount++;
            return null; // Ignorer ce doublon existant
        }
        
        // Ajouter le numéro normalisé à la liste des numéros traités
        $this->processedPhones[] = $normalizedPhone;
        
        // Créer le client
        return new Client([
            'nom_complet'      => $row['nom_complet'],
            'numero_telephone' => $row['numero_telephone'],
            'adresse_mail'     => isset($row['adresse_mail']) && !empty($row['adresse_mail']) ? $row['adresse_mail'] : null,
            'date_naissance'   => isset($row['date_naissance']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_naissance'])->format('Y-m-d') : null,
            'points'           => isset($row['points']) ? $row['points'] : 0,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nom_complet' => 'required|string|max:255',
            'numero_telephone' => 'required|string|max:255',
            'adresse_mail' => 'nullable|email|max:255',
            'date_naissance' => 'nullable',
            'points' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nom_complet.required' => 'Le nom complet est obligatoire',
            'numero_telephone.required' => 'Le numéro de téléphone est obligatoire',
            'adresse_mail.email' => 'Si fournie, l\'adresse email doit être valide',
            'points.numeric' => 'Les points doivent être un nombre',
            'points.min' => 'Les points ne peuvent pas être négatifs',
        ];
    }
    
    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }
    
    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
    
    /**
     * Obtenir le nombre de doublons détectés
     *
     * @return int
     */
    public function getDuplicatesCount(): int
    {
        return $this->duplicatesCount;
    }
}
