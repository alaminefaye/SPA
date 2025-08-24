<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RappelRendezVous extends Model
{
    protected $table = 'rappels_rendez_vous';
    
    protected $fillable = [
        'client_id',
        'seance_id',
        'date_prevue',
        'heure_prevue',
        'confirme',
        'rappel_envoye',
        'statut',
        'commentaire'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_prevue' => 'date',
        'heure_prevue' => 'datetime:H:i',
        'confirme' => 'boolean',
        'rappel_envoye' => 'boolean',
    ];
    
    /**
     * Get the client associated with the rappel.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Get the seance that generated this rappel.
     */
    public function seance()
    {
        return $this->belongsTo(Seance::class);
    }
    
    /**
     * Détermine si le rappel est pour aujourd'hui
     *
     * @return bool
     */
    public function estPourAujourdhui()
    {
        return $this->date_prevue->isToday();
    }
    
    /**
     * Détermine si le rappel est pour cette semaine
     *
     * @return bool
     */
    public function estPourCetteSemaine()
    {
        $today = now();
        $endOfWeek = $today->copy()->endOfWeek();
        
        return $this->date_prevue->between($today, $endOfWeek);
    }
    
    /**
     * Retourne la date et l'heure formatées
     *
     * @return string
     */
    public function getDateHeureFormatee()
    {
        return $this->date_prevue->format('d/m/Y') . ' à ' . $this->heure_prevue->format('H:i');
    }
    
    /**
     * Détermine si le rappel est prévu dans exactement 2 jours
     * 
     * @return bool
     */
    public function estPourDansDeuxJours()
    {
        $dateDansDeuxJours = now()->addDays(2)->startOfDay();
        return $this->date_prevue->startOfDay()->equalTo($dateDansDeuxJours);
    }
    
    /**
     * Calcule le nombre de jours restants avant le rendez-vous
     * 
     * @return int
     */
    public function getJoursRestantsAttribute()
    {
        return now()->startOfDay()->diffInDays($this->date_prevue->startOfDay(), false);
    }
    
    /**
     * Génère l'URL pour envoyer un message WhatsApp au client
     * 
     * @return string
     */
    public function getWhatsAppUrl()
    {
        $numero = $this->formatTelephoneForWhatsApp($this->client->numero_telephone);
        $message = $this->genererMessageWhatsApp();
        
        return "https://api.whatsapp.com/send?phone=".$numero."&text=".urlencode($message);
    }
    
    /**
     * Formate le numéro de téléphone pour WhatsApp (sans espaces, avec code pays +225)
     * 
     * @param string $telephone
     * @return string
     */
    protected function formatTelephoneForWhatsApp($telephone)
    {
        // Enlever tous les espaces et caractères non-numériques
        $numero = preg_replace('/[^0-9]/', '', $telephone);
        
        // Formater selon le format requis: +225 0XXXXXXXX
        
        // 1. Si commence déjà par 225 et sans 0 après, ajouter le 0
        if (substr($numero, 0, 3) === '225' && substr($numero, 3, 1) !== '0') {
            $numero = '2250' . substr($numero, 3);
        }
        // 2. Si commence par 00225, reformater correctement
        else if (substr($numero, 0, 5) === '00225') {
            $numero = '2250' . substr($numero, 5);
        }
        // 3. Si commence déjà par 0, ajouter 225 avant
        else if (substr($numero, 0, 1) === '0') {
            $numero = '225' . $numero;
        }
        // 4. Si ne commence pas par 225, ajouter 2250
        else if (substr($numero, 0, 3) !== '225') {
            $numero = '2250' . $numero;
        }
        
        // Vérifier que le format est bien 2250XXXXXXXX
        if (substr($numero, 0, 4) !== '2250') {
            // Si commence par 225 mais sans 0, ajouter le 0
            if (substr($numero, 0, 3) === '225') {
                $numero = '2250' . substr($numero, 3);
            }
        }
        
        return $numero;
    }
    
    /**
     * Génère un message personnalisé pour WhatsApp
     * 
     * @return string
     */
    protected function genererMessageWhatsApp()
    {
        $nomClient = $this->client->nom_complet;
        $dateRdv = $this->date_prevue->format('d/m/Y');
        $heureRdv = $this->heure_prevue->format('H:i');
        $joursRestants = $this->jours_restants;
        
        $message = "Bonjour $nomClient, \n\n";
        $message .= "C'est JARED SPA qui vous contacte pour vous rappeler votre prochain rendez-vous prévu le $dateRdv à $heureRdv. ";
        
        if ($joursRestants > 1) {
            $message .= "Il vous reste $joursRestants jours avant votre rendez-vous.";
        } elseif ($joursRestants == 1) {
            $message .= "Votre rendez-vous est prévu pour demain.";
        } elseif ($joursRestants == 0) {
            $message .= "Votre rendez-vous est prévu pour aujourd'hui.";
        }
        
        $message .= "\n\nNous sommes impatients de vous accueillir!\n\nL'équipe JARED SPA 💅";
        
        return $message;
    }
}
