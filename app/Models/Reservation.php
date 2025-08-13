<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Reservation extends Model
{
    use LogsActivity;
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
    
    /**
     * Configure les options de journalisation d'activité
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['client_id', 'salon_id', 'prix', 'duree', 'date_heure', 'statut', 'commentaire', 'client_created', 'is_read'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                $desc = "La réservation a été ";
                switch($eventName) {
                    case 'created':
                        $desc .= "créée";
                        break;
                    case 'updated':
                        $desc .= "modifiée";
                        break;
                    case 'deleted':
                        $desc .= "supprimée";
                        break;
                }
                return $desc;
            });
    }
}
