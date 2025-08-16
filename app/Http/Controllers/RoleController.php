<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Constructor pour appliquer les middlewares d'authentification et d'autorisation
     */
    public function __construct()
    {
        // Aucun middleware direct ici - ils seront appliqués dans les routes
    }
    
    /**
     * Liste tous les rôles
     */
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Affiche le formulaire de création d'un rôle
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($item, $key) {
            // Group by permission type (extract word before first space)
            $parts = explode(' ', $item->name);
            return $parts[1] ?? 'other';
        });
        
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Enregistre un nouveau rôle
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Affiche un rôle spécifique
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $users = $role->users()->paginate(10);
        
        return view('admin.roles.show', compact('role', 'rolePermissions', 'users'));
    }

    /**
     * Affiche le formulaire d'édition d'un rôle
     */
    public function edit(Role $role)
    {
        if ($role->name === 'super-admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Le rôle super-admin ne peut pas être modifié.');
        }
        
        $permissions = Permission::all()->groupBy(function ($item, $key) {
            $parts = explode(' ', $item->name);
            return $parts[1] ?? 'other';
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Met à jour un rôle
     */
    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super-admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Le rôle super-admin ne peut pas être modifié.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Supprime un rôle
     */
    public function destroy(Role $role)
    {
        if ($role->name === 'super-admin' || $role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Les rôles par défaut ne peuvent pas être supprimés.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Ce rôle ne peut pas être supprimé car il est attribué à des utilisateurs.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }
}
