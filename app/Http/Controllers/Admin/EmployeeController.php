<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with('salon');
        
        // Recherche par nom/prénom
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('numero_telephone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtre par poste
        if ($request->filled('poste')) {
            $query->where('poste', $request->input('poste'));
        }
        
        // Filtre par salon
        if ($request->filled('salon_id')) {
            $query->where('salon_id', $request->input('salon_id'));
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('actif', $request->input('status') === 'active' ? 1 : 0);
        }
        
        $employees = $query->orderBy('nom')->paginate(15)->withQueryString();
        
        // Récupérer les salons et les postes pour les filtres
        $salons = Salon::orderBy('nom')->pluck('nom', 'id');
        $postes = [
            'Esthéticienne' => 'Esthéticienne',
            'Coiffeur/Coiffeuse' => 'Coiffeur/Coiffeuse',
            'Masseur/Masseuse' => 'Masseur/Masseuse',
            'Manucure' => 'Manucure',
            'Pédicure' => 'Pédicure',
            'Réceptionniste' => 'Réceptionniste',
            'Manager' => 'Manager',
            'Directeur/Directrice' => 'Directeur/Directrice',
        ];
        
        return view('admin.employees.index', compact('employees', 'salons', 'postes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $salons = Salon::orderBy('nom')->pluck('nom', 'id');
        $postes = [
            'Esthéticienne' => 'Esthéticienne',
            'Coiffeur/Coiffeuse' => 'Coiffeur/Coiffeuse',
            'Masseur/Masseuse' => 'Masseur/Masseuse',
            'Manucure' => 'Manucure',
            'Pédicure' => 'Pédicure',
            'Réceptionniste' => 'Réceptionniste',
            'Manager' => 'Manager',
            'Directeur/Directrice' => 'Directeur/Directrice',
        ];
        
        return view('admin.employees.create', compact('salons', 'postes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'numero_telephone' => 'required|string|max:20|unique:employees',
            'email' => 'nullable|email|unique:employees',
            'adresse' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'date_embauche' => 'required|date',
            'poste' => 'required|string|max:100',
            'specialites' => 'nullable|string',
            'salaire' => 'nullable|numeric|min:0',
            'salon_id' => 'nullable|exists:salons,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'actif' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $photoPath;
        }
        
        $employee = Employee::create($validated);
        
        return redirect()->route('admin.employees.show', $employee)
            ->with('success', 'Employé(e) ajouté(e) avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $activities = \Spatie\Activitylog\Models\Activity::causedBy($employee)
            ->orWhere(function($query) use ($employee) {
                $query->where('subject_type', 'App\\Models\\Employee')
                      ->where('subject_id', $employee->id);
            })
            ->latest()
            ->get();
            
        return view('admin.employees.show', compact('employee', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $salons = Salon::orderBy('nom')->pluck('nom', 'id');
        $postes = [
            'Esthéticienne' => 'Esthéticienne',
            'Coiffeur/Coiffeuse' => 'Coiffeur/Coiffeuse',
            'Masseur/Masseuse' => 'Masseur/Masseuse',
            'Manucure' => 'Manucure',
            'Pédicure' => 'Pédicure',
            'Réceptionniste' => 'Réceptionniste',
            'Manager' => 'Manager',
            'Directeur/Directrice' => 'Directeur/Directrice',
        ];
        
        return view('admin.employees.edit', compact('employee', 'salons', 'postes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'numero_telephone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees')->ignore($employee->id),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('employees')->ignore($employee->id),
            ],
            'adresse' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'date_embauche' => 'required|date',
            'poste' => 'required|string|max:100',
            'specialites' => 'nullable|string',
            'salaire' => 'nullable|numeric|min:0',
            'salon_id' => 'nullable|exists:salons,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'actif' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo s'il en existe une
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }
            
            $photoPath = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $photoPath;
        }
        
        $employee->update($validated);
        
        return redirect()->route('admin.employees.show', $employee)
            ->with('success', 'Informations de l\'employé(e) mises à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')
            ->with('success', 'Employé(e) supprimé(e) avec succès');
    }
    
    /**
     * Changer l'état actif/inactif de l'employé
     */
    public function toggleStatus(Employee $employee)
    {
        $employee->actif = !$employee->actif;
        $employee->save();
        
        $status = $employee->actif ? 'activé' : 'désactivé';
        
        return redirect()->back()
            ->with('success', "Le statut de l'employé(e) a été $status avec succès");
    }
}
