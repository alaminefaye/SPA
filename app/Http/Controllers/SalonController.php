<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Salon::query();
        
        if ($search) {
            $query->where('nom', 'LIKE', "%{$search}%");
        }
        
        $salons = $query->paginate(10)->withQueryString();
        
        return view('salons.index', compact('salons', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('salons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:salons',
        ]);

        Salon::create([
            'nom' => $request->nom,
        ]);

        return redirect()->route('salons.index')
            ->with('success', 'Salon créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $salon = Salon::findOrFail($id);
        return view('salons.show', compact('salon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $salon = Salon::findOrFail($id);
        return view('salons.edit', compact('salon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $salon = Salon::findOrFail($id);

        $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('salons')->ignore($salon->id),
            ],
        ]);

        $salon->update([
            'nom' => $request->nom,
        ]);

        return redirect()->route('salons.index')
            ->with('success', 'Salon modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $salon = Salon::findOrFail($id);
        $salon->delete();

        return redirect()->route('salons.index')
            ->with('success', 'Salon supprimé avec succès');
    }
}
