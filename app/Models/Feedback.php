<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Salon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Feedback extends Model
{
    use HasFactory, LogsActivity;
    
    protected $table = 'feedbacks';
    
    protected $fillable = [
        'nom_complet',
        'telephone',
        'email',
        'salon_id',
        'numero_ticket',
        'prestation',
        'sujet',
        'photo',
        'message',
        'is_priority',
        'is_read'
    ];
    
    protected $casts = [
        'is_priority' => 'boolean',
        'is_read' => 'boolean',
    ];
    
    /**
     * Get the salon associated with the feedback
     */
    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
    
    /**
     * Configure les options de journalisation d'activité
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom_complet', 'telephone', 'email', 'salon_id', 'numero_ticket', 'prestation', 'sujet', 'photo', 'message', 'is_priority', 'is_read'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                $desc = "Le feedback a été ";
                switch($eventName) {
                    case 'created':
                        $desc .= "créé";
                        break;
                    case 'updated':
                        $desc .= "modifié";
                        break;
                    case 'deleted':
                        $desc .= "supprimé";
                        break;
                }
                return $desc;
            });
    }
}
