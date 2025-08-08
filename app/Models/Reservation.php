<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    protected $fillable = [
        'client_id',
        'salon_id',
        'prix',
        'duree',
        'date_heure',
        'statut',
        'commentaire',
        'client_created',
        'is_read'
    ];
    
    protected $casts = [
        'duree' => 'datetime:H:i:s',
        'prix' => 'decimal:2',
        'date_heure' => 'datetime',
        'client_created' => 'boolean',
        'is_read' => 'boolean'
    ];
    
    /**
     * Get the client associated with the reservation.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Get the salon associated with the reservation.
     */
    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }
    
    /**
     * Get the prestations associated with the reservation.
     */
    public function prestations(): BelongsToMany
    {
        return $this->belongsToMany(Prestation::class, 'reservation_prestation');
    }
}
