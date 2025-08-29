<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Liste des clients à partir de la capture d'écran
        $clients = [
            ['nom_complet' => 'MME ARCHER', 'numero_telephone' => '07 49 29 86 45'],
            ['nom_complet' => 'MME AKOUN AIMEE', 'numero_telephone' => '07 03 59 19 31'],
            ['nom_complet' => 'MME KONE FOUSNGUE', 'numero_telephone' => '07 47 33 94 99'],
            ['nom_complet' => 'MME GBOHO MARLAINE', 'numero_telephone' => '07 09 61 00 67'],
            ['nom_complet' => 'MME DOUKOURE', 'numero_telephone' => '07 48 81 71 47'],
            ['nom_complet' => 'MME NOURA', 'numero_telephone' => '07 85 34 55 15'],
            ['nom_complet' => 'MME YAO', 'numero_telephone' => '07 09 49 03 45'],
            ['nom_complet' => 'MME ALLO', 'numero_telephone' => '07 08 83 17 60'],
            ['nom_complet' => 'MME IRIE RAISSA', 'numero_telephone' => '07 07 53 24 88'],
            ['nom_complet' => 'MME STEPHANIE', 'numero_telephone' => '07 78 92 20 88'],
            ['nom_complet' => 'MME MURIELLE MICHO', 'numero_telephone' => '07 47 07 18 23'],
            ['nom_complet' => 'MME OUATTARA AMINATA', 'numero_telephone' => '05 46 45 96 86'],
            ['nom_complet' => 'MME DORIGENE', 'numero_telephone' => '07 07 55 44 13'],
            ['nom_complet' => 'MME INES KOUAME', 'numero_telephone' => '01 42 63 07 96'],
            ['nom_complet' => 'M.KARIM', 'numero_telephone' => '01 42 93 12 13'],
            ['nom_complet' => 'MME AKOUN', 'numero_telephone' => '07 49 79 41 86'],
            ['nom_complet' => 'MME KRAMO', 'numero_telephone' => '01 52 78 00 51'],
            ['nom_complet' => 'Mme Maimouna', 'numero_telephone' => '07 57 56 18 41'],
            ['nom_complet' => 'Mme Tall Olivia', 'numero_telephone' => '01 01 56 45 81'],
            ['nom_complet' => 'Mme Dominique', 'numero_telephone' => '07 67 91 50 51'],
            ['nom_complet' => 'Mme Mercedes', 'numero_telephone' => '07 08 88 96 31'],
            ['nom_complet' => 'Mme Fatim Traoré', 'numero_telephone' => '07 08 50 17 61'],
            ['nom_complet' => 'Mme Gnoka', 'numero_telephone' => '07 87 90 47 62'],
            ['nom_complet' => 'Mme Sylla', 'numero_telephone' => '07 07 60 08 20'],
            ['nom_complet' => 'Mme Kouassi', 'numero_telephone' => '07 47 18 12 75'],
            ['nom_complet' => 'Mme Tchan', 'numero_telephone' => '07 77 19 98 22'],
            ['nom_complet' => 'Mme Ezoua', 'numero_telephone' => '07 49 15 77 63'],
            ['nom_complet' => 'Mme Coulibalyn', 'numero_telephone' => '07 09 08 53 07'],
            ['nom_complet' => 'Mlle Faye Anaïs', 'numero_telephone' => '07 88 03 42 49'],
            ['nom_complet' => 'Mlle Komrany Océane', 'numero_telephone' => '07 41 38 16 62'],
            ['nom_complet' => 'Mme Da Silvera', 'numero_telephone' => '07 57 91 12 70'],
            ['nom_complet' => 'Mme Lioue Carole nadia', 'numero_telephone' => '07 49 68 64 83'],
            ['nom_complet' => 'Mme Ami', 'numero_telephone' => '07 09 41 91 51'],
            ['nom_complet' => 'M.Abdjas', 'numero_telephone' => '05 05 99 34 93'],
            ['nom_complet' => 'Mme Fofana Fatim', 'numero_telephone' => '05 59 55 75 89'],
            ['nom_complet' => 'M.Coulibaly Issiaka', 'numero_telephone' => '07 09 44 91 46'],
            ['nom_complet' => 'Mme Yao joëlle', 'numero_telephone' => '07 77 71 11 25'],
            ['nom_complet' => 'Mme Diabaté Natacha', 'numero_telephone' => '07 77 33 19 70'],
            ['nom_complet' => 'Mme Tetty', 'numero_telephone' => '07 79 51 44 04'],
            ['nom_complet' => 'Mme Dorrell', 'numero_telephone' => '07 11 10 99 12'],
            ['nom_complet' => 'Mme Grace', 'numero_telephone' => '07 59 47 13 76'],
            ['nom_complet' => 'Mme Kouassi Mireille', 'numero_telephone' => '07 08 78 78 99'],
            ['nom_complet' => 'Mme Ines', 'numero_telephone' => '07 77 32 90 52'],
            ['nom_complet' => 'Mme Mobio evelyne', 'numero_telephone' => '07 89 01 01 37'],
            ['nom_complet' => 'Mme Bakaroto', 'numero_telephone' => '07 07 58 10 59'],
            ['nom_complet' => 'Mme Tagba Angie Annick', 'numero_telephone' => '01 02 64 21 81'],
            ['nom_complet' => 'Mme LEADS', 'numero_telephone' => '07 78 90 88 12'],
            ['nom_complet' => 'M.Amaml jean', 'numero_telephone' => '01 42 41 05 61'],
            ['nom_complet' => 'Mme anita', 'numero_telephone' => '07 57 39 87 49'],
        ];

        // Insérer les clients dans la base de données
        foreach ($clients as $clientData) {
            Client::create([
                'nom_complet' => $clientData['nom_complet'],
                'numero_telephone' => $clientData['numero_telephone'],
                'adresse_mail' => null, // Email est maintenant optionnel
                'points' => 0,
                'date_naissance' => null, // Date de naissance est optionnelle
            ]);
        }
    }
}
