<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seance extends Model
{
    protected $fillable = [
        'client_id',
        'salon_id',
        'prestation_id',
        'prix',
        'duree',
        'statut',
        'commentaire',
    ];
    
    protected $casts = [
        'duree' => 'datetime:H:i:s',
        'prix' => 'decimal:2',
    ];
    
    /**
     * Get the client associated with the seance.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Get the salon associated with the seance.
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }
    
    /**
     * Get the prestation associated with the seance.
     */
    public function prestation(): BelongsTo
    {
        return $this->belongsTo(Prestation::class);
    }
}
