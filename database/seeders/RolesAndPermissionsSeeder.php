<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser les rôles et permissions mis en cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Création des permissions
        // Permissions pour Clients
        Permission::firstOrCreate(['name' => 'view clients']);
        Permission::firstOrCreate(['name' => 'create clients']);
        Permission::firstOrCreate(['name' => 'edit clients']);
        Permission::firstOrCreate(['name' => 'delete clients']);

        // Permissions pour Séances
        Permission::firstOrCreate(['name' => 'view seances']);
        Permission::firstOrCreate(['name' => 'create seances']);
        Permission::firstOrCreate(['name' => 'edit seances']);
        Permission::firstOrCreate(['name' => 'delete seances']);

        // Permissions pour Produits
        Permission::firstOrCreate(['name' => 'view products']);
        Permission::firstOrCreate(['name' => 'create products']);
        Permission::firstOrCreate(['name' => 'edit products']);
        Permission::firstOrCreate(['name' => 'delete products']);
        Permission::firstOrCreate(['name' => 'manage stock']);

        // Permissions pour Catégories de produits
        Permission::firstOrCreate(['name' => 'view product categories']);
        Permission::firstOrCreate(['name' => 'create product categories']);
        Permission::firstOrCreate(['name' => 'edit product categories']);
        Permission::firstOrCreate(['name' => 'delete product categories']);

        // Permissions pour Prestations
        Permission::firstOrCreate(['name' => 'view prestations']);
        Permission::firstOrCreate(['name' => 'create prestations']);
        Permission::firstOrCreate(['name' => 'edit prestations']);
        Permission::firstOrCreate(['name' => 'delete prestations']);

        // Permissions pour Salons
        Permission::firstOrCreate(['name' => 'view salons']);
        Permission::firstOrCreate(['name' => 'create salons']);
        Permission::firstOrCreate(['name' => 'edit salons']);
        Permission::firstOrCreate(['name' => 'delete salons']);

        // Permissions pour Réservations
        Permission::firstOrCreate(['name' => 'view reservations']);
        Permission::firstOrCreate(['name' => 'create reservations']);
        Permission::firstOrCreate(['name' => 'edit reservations']);
        Permission::firstOrCreate(['name' => 'delete reservations']);
        Permission::firstOrCreate(['name' => 'mark reservation as read']);

        // Permissions pour Achats
        Permission::firstOrCreate(['name' => 'view purchases']);
        Permission::firstOrCreate(['name' => 'create purchases']);
        Permission::firstOrCreate(['name' => 'edit purchases']);
        Permission::firstOrCreate(['name' => 'delete purchases']);

        // Permissions pour Feedback
        Permission::firstOrCreate(['name' => 'view feedbacks']);
        Permission::firstOrCreate(['name' => 'create feedbacks']);
        Permission::firstOrCreate(['name' => 'edit feedbacks']);
        Permission::firstOrCreate(['name' => 'delete feedbacks']);
        Permission::firstOrCreate(['name' => 'mark feedback as read']);

        // Permissions pour Activity Logs
        Permission::firstOrCreate(['name' => 'view activity logs']);
        Permission::firstOrCreate(['name' => 'delete activity logs']);
        Permission::firstOrCreate(['name' => 'clear all activity logs']);

        // Permissions pour Rapports
        Permission::firstOrCreate(['name' => 'view reports']);
        Permission::firstOrCreate(['name' => 'export reports']);

        // Permissions pour Users
        Permission::firstOrCreate(['name' => 'view users']);
        Permission::firstOrCreate(['name' => 'create users']);
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'delete users']);

        // Permissions pour Roles et Permissions
        Permission::firstOrCreate(['name' => 'view roles']);
        Permission::firstOrCreate(['name' => 'create roles']);
        Permission::firstOrCreate(['name' => 'edit roles']);
        Permission::firstOrCreate(['name' => 'delete roles']);
        Permission::firstOrCreate(['name' => 'assign roles']);

        // Création des rôles et assignation des permissions
        // Rôle Super Admin - a toutes les permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        // Attribuer toutes les permissions au rôle super-admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Rôle Admin - peut gérer presque tout sauf les rôles/permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view clients', 'create clients', 'edit clients', 'delete clients',
            'view seances', 'create seances', 'edit seances', 'delete seances',
            'view products', 'create products', 'edit products', 'delete products', 'manage stock',
            'view product categories', 'create product categories', 'edit product categories', 'delete product categories',
            'view prestations', 'create prestations', 'edit prestations', 'delete prestations',
            'view salons', 'create salons', 'edit salons', 'delete salons',
            'view reservations', 'create reservations', 'edit reservations', 'delete reservations', 'mark reservation as read',
            'view purchases', 'create purchases', 'edit purchases', 'delete purchases',
            'view feedbacks', 'create feedbacks', 'edit feedbacks', 'delete feedbacks', 'mark feedback as read',
            'view activity logs', 'delete activity logs', 'clear all activity logs',
            'view users', 'create users', 'edit users', 'delete users',
            'view reports', 'export reports'
        ]);

        // Rôle Manager - peut gérer les opérations quotidiennes mais pas les configurations
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view clients', 'create clients', 'edit clients',
            'view seances', 'create seances', 'edit seances',
            'view products', 'create products', 'edit products', 'manage stock',
            'view product categories',
            'view prestations',
            'view salons',
            'view reservations', 'create reservations', 'edit reservations', 'mark reservation as read',
            'view purchases', 'create purchases', 'edit purchases',
            'view feedbacks', 'mark feedback as read',
            'view activity logs',
            'view reports', 'export reports'
        ]);

        // Rôle Réceptionniste - gérer les clients et réservations
        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist']);
        $receptionistRole->givePermissionTo([
            'view clients', 'create clients', 'edit clients',
            'view seances',
            'view products',
            'view prestations',
            'view salons',
            'view reservations', 'create reservations', 'edit reservations', 'mark reservation as read',
            'view purchases', 'create purchases',
            'view feedbacks', 'create feedbacks', 'mark feedback as read',
            'view reports'
        ]);

        // Rôle Esthéticien - gérer les séances
        $estheticianRole = Role::firstOrCreate(['name' => 'esthetician']);
        $estheticianRole->givePermissionTo([
            'view clients',
            'view seances', 'edit seances',
            'view prestations',
            'view salons'
        ]);

        // Attribuer le rôle super-admin à l'utilisateur admin initial
        $adminUser = User::where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }
    }
}
