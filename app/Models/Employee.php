<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use SoftDeletes, LogsActivity;
    
    protected $fillable = [
        'nom',
        'prenom',
        'numero_telephone',
        'email',
        'adresse',
        'date_naissance',
        'date_embauche',
        'poste',
        'specialites',
        'salaire',
        'salon_id',
        'photo',
        'actif',
        'notes'
    ];
    
    protected $casts = [
        'date_naissance' => 'date',
        'date_embauche' => 'date',
        'actif' => 'boolean',
        'salaire' => 'decimal:2'
    ];
    
    /**
     * Configure les options de journalisation pour Spatie Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nom', 'prenom', 'numero_telephone', 'email', 'poste', 
                'specialites', 'salaire', 'salon_id', 'actif'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    /**
     * Relation avec le salon où l'employé travaille
     */
    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
    
    /**
     * Relation avec les séances réalisées par l'employé
     */
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
    
    /**
     * Obtenir le nom complet de l'employé
     */
    public function getNomCompletAttribute()
    {
        return "$this->prenom $this->nom";
    }
}
