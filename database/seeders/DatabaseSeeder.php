<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            RolesAndPermissionsSeeder::class, // Exécuter le seeder des rôles et permissions après la création des utilisateurs admin
            PrestationSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
