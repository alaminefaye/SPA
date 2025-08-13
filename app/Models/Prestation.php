<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Prestation extends Model
{
    use LogsActivity;
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
    
    /**
     * Configure les options de journalisation d'activité
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom_prestation', 'prix', 'duree'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                $desc = "La prestation a été ";
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
