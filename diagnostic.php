<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Vérifier les utilisateurs avec leurs rôles
echo "=== UTILISATEURS ET LEURS RÔLES ===\n";
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo "Utilisateur: {$user->name} ({$user->email})\n";
    echo "Rôles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    echo "Permission 'view roles': " . ($user->can('view roles') ? 'OUI' : 'NON') . "\n";
    echo "--------------------\n";
}

// 2. Vérifier les rôles existants et leurs permissions
echo "\n=== RÔLES ET LEURS PERMISSIONS ===\n";
$roles = \Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    echo "Rôle: {$role->name}\n";
    echo "Permissions: " . implode(', ', $role->permissions->pluck('name')->toArray()) . "\n";
    echo "--------------------\n";
}
