<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
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
            'adresse_mail' => 'nullable|email|max:255|unique:clients,adresse_mail',
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
            'adresse_mail.unique' => 'Cette adresse email est déjà utilisée',
            'points.numeric' => 'Les points doivent être un nombre',
            'points.min' => 'Les points ne peuvent pas être négatifs',
        ];
    }
}
