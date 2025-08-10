<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Salon;
use App\Models\Prestation;
use App\Services\PriorityDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedbackController extends Controller
{
    /**
     * Service de détection de priorité
     */
    protected $priorityService;
    
    /**
     * Constructeur
     */
    public function __construct(PriorityDetectionService $priorityService)
    {
        $this->priorityService = $priorityService;
    }
    
    /**
     * Display the public feedback form
     */
    public function showPublicForm()
    {
        $salons = Salon::all();
        $prestations = Prestation::all();
        return view('feedbacks.public-form', compact('salons', 'prestations'));
    }

    /**
     * Display a listing of feedbacks in admin dashboard
     */
    public function index()
    {
        $feedbacks = Feedback::orderBy('is_priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('feedbacks.index', compact('feedbacks'));
    }

    /**
     * Show the form for creating a new feedback (admin)
     */
    public function create()
    {
        $salons = Salon::all();
        return view('feedbacks.create', compact('salons'));
    }

    /**
     * Store a newly created feedback from public form or admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'salon_id' => 'nullable|exists:salons,id',
            'numero_ticket' => 'nullable|string|max:50',
            'prestation' => 'nullable|string|max:255',
            'sujet' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'message' => 'required|string',
        ]);

        // Gérer l'upload de photo si présent
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('feedbacks', 'public');
            $validated['photo'] = $photoPath;
        }

        // Créer le feedback
        $feedback = new Feedback($validated);
        
        // Vérifier les mots-clés prioritaires avec notre service
        $feedback->is_priority = $this->priorityService->isPriority($feedback->sujet, $feedback->message);
        $feedback->is_read = false; // Par défaut, non lu
        
        $feedback->save();

        // Rediriger selon la source
        if ($request->has('from_admin')) {
            return redirect()->route('feedbacks.index')
                ->with('success', 'Suggestion ou préoccupation ajoutée avec succès');
        }

        // Réponse pour formulaire public
        return redirect()->back()
            ->with('success', 'Votre message a été envoyé avec succès. Merci pour votre contribution!');
    }

    /**
     * Display the specified feedback
     */
    public function show(Feedback $feedback)
    {
        // Marquer comme lu si ce n'était pas déjà le cas
        if (!$feedback->is_read) {
            $feedback->is_read = true;
            $feedback->save();
        }
        
        return view('feedbacks.show', compact('feedback'));
    }

    /**
     * Mark feedback as read
     */
    public function markAsRead(Feedback $feedback)
    {
        $feedback->is_read = true;
        $feedback->save();
        
        return redirect()->back()->with('success', 'Marqué comme lu');
    }

    /**
     * Toggle priority status
     */
    public function togglePriority(Feedback $feedback)
    {
        $feedback->is_priority = !$feedback->is_priority;
        $feedback->save();
        
        $status = $feedback->is_priority ? 'prioritaire' : 'normale';
        
        return redirect()->back()
            ->with('success', "Suggestion maintenant en priorité $status");
    }

    /**
     * Remove the specified feedback
     */
    public function destroy(Feedback $feedback)
    {
        // Supprimer la photo si elle existe
        if ($feedback->photo) {
            Storage::disk('public')->delete($feedback->photo);
        }
        
        $feedback->delete();
        
        return redirect()->route('feedbacks.index')
            ->with('success', 'Suggestion supprimée avec succès');
    }
}
