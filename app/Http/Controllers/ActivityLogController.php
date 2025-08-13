<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Afficher la liste des logs d'activité
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $logs = Activity::latest()->paginate(20);
        return view('logs.index', compact('logs'));
    }
    
    /**
     * Afficher les détails d'un log d'activité
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $log = Activity::findOrFail($id);
        return view('logs.show', compact('log'));
    }
    
    /**
     * Supprimer un log d'activité
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $log = Activity::findOrFail($id);
        $log->delete();
        
        return redirect()->route('activity.index')->with('success', 'Log d\'activité supprimé avec succès');
    }
    
    /**
     * Vider tous les logs d'activité
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearAll()
    {
        Activity::truncate();
        
        return redirect()->route('activity.index')->with('success', 'Tous les logs d\'activité ont été supprimés');
    }
}
