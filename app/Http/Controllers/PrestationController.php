<?php

namespace App\Http\Controllers;

use App\Models\Prestation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrestationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Prestation::query();
        
        if ($search) {
            $query->where('nom_prestation', 'LIKE', "%{$search}%");
        }
        
        $prestations = $query->paginate(10)->withQueryString();
        
        return view('prestations.index', compact('prestations', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('prestations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_prestation' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'duree' => 'required|date_format:H:i:s',
        ]);

        Prestation::create([
            'nom_prestation' => $request->nom_prestation,
            'prix' => $request->prix,
            'duree' => $request->duree,
        ]);

        return redirect()->route('prestations.index')
            ->with('success', 'Prestation créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prestation = Prestation::findOrFail($id);
        return view('prestations.show', compact('prestation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $prestation = Prestation::findOrFail($id);
        return view('prestations.edit', compact('prestation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prestation = Prestation::findOrFail($id);

        $request->validate([
            'nom_prestation' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'duree' => 'required|date_format:H:i:s',
        ]);

        $prestation->update([
            'nom_prestation' => $request->nom_prestation,
            'prix' => $request->prix,
            'duree' => $request->duree,
        ]);

        return redirect()->route('prestations.index')
            ->with('success', 'Prestation modifiée avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prestation = Prestation::findOrFail($id);
        $prestation->delete();

        return redirect()->route('prestations.index')
            ->with('success', 'Prestation supprimée avec succès');
    }
}
