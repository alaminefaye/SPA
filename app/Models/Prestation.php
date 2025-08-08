<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function seances(): BelongsToMany
    {
        return $this->belongsToMany(Seance::class, 'seance_prestation')
                    ->withTimestamps();
    }
    
    /**
     * Get the reservations that use this prestation.
     */
    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(Reservation::class, 'reservation_prestation');
    }
}
