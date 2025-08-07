<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestation extends Model
{
    protected $fillable = [
        'nom_prestation',
        'prix',
        'duree'
    ];
    
    protected $casts = [
        'duree' => 'datetime:H:i:s',
    ];
    
    /**
     * Get the seances that use this prestation.
     */
    public function seances()
    {
        return $this->belongsToMany(Seance::class, 'seance_prestation')
                    ->withPivot('quantite')
                    ->withTimestamps();
    }
}
