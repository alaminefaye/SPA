<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Aucun middleware direct ici - ils seront appliqués dans les routes
    }

    /**
     * Liste toutes les permissions avec option de recherche
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Permission::query();
        
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        $permissions = $query->get()->groupBy(function ($item, $key) {
            $parts = explode(' ', $item->name);
            return $parts[1] ?? 'other';
        });
        
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Affiche une permission spécifique
     */
    public function show(Permission $permission)
    {
        $roles = $permission->roles;
        $users = $permission->users();
        
        return view('admin.permissions.show', compact('permission', 'roles', 'users'));
    }
}
