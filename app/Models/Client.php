<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nom_complet',
        'numero_telephone',
        'adresse_mail',
    ];
}
