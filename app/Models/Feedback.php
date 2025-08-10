<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Salon;

class Feedback extends Model
{
    use HasFactory;
    
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
    

}
