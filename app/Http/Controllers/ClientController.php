<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Client::query();
        
        if ($search) {
            $query->where('nom_complet', 'LIKE', "%{$search}%")
                  ->orWhere('numero_telephone', 'LIKE', "%{$search}%")
                  ->orWhere('adresse_mail', 'LIKE', "%{$search}%");
        }
        
        $clients = $query->paginate(10)->withQueryString();
        
        return view('clients.index', compact('clients', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'numero_telephone' => 'required|string|max:255',
            'adresse_mail' => 'required|email|max:255|unique:clients',
        ]);

        Client::create([
            'nom_complet' => $request->nom_complet,
            'numero_telephone' => $request->numero_telephone,
            'adresse_mail' => $request->adresse_mail,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'numero_telephone' => 'required|string|max:255',
            'adresse_mail' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients')->ignore($client->id),
            ],
        ]);

        $client->update([
            'nom_complet' => $request->nom_complet,
            'numero_telephone' => $request->numero_telephone,
            'adresse_mail' => $request->adresse_mail,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès');
    }
}
