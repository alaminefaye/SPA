<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Feedback;
use App\Models\Salon;

class EmployeeFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds to create test feedback data with employee associations.
     *
     * @return void
     */
    public function run()
    {
        // Get all employees
        $employees = Employee::all();
        
        if ($employees->isEmpty()) {
            $this->command->error('No employees found. Please create employees first.');
            return;
        }
        
        // Get all salons
        $salons = Salon::all();
        
        if ($salons->isEmpty()) {
            $this->command->error('No salons found. Please create salons first.');
            return;
        }
        
        $this->command->info('Creating test feedback data with employee associations...');
        
        // Create 50 feedbacks with random employee associations
        for ($i = 1; $i <= 50; $i++) {
            $employee = $employees->random();
            $salon = $salons->random();
            
            $feedback = Feedback::create([
                'nom_complet' => 'Client Test ' . $i,
                'telephone' => '+221 7' . rand(0, 9) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'email' => 'client' . $i . '@example.com',
                'salon_id' => $salon->id,
                'employee_id' => $employee->id,
                'numero_ticket' => 'FB-' . date('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'prestation' => 'Prestation Test ' . rand(1, 5),
                'sujet' => 'Feedback sur service',
                'message' => 'Commentaire sur le service reçu et l\'expérience client avec l\'employé.',
                'is_priority' => rand(0, 1),
                'is_read' => rand(0, 1),
                'satisfaction_rating' => rand(1, 5),
                'created_at' => now()->subDays(rand(0, 90)) // Random date within the last 90 days
            ]);
            
            $this->command->info("Created feedback #{$i} with rating {$feedback->satisfaction_rating} for employee {$employee->prenom} {$employee->nom}");
        }
        
        $this->command->info('Successfully created 50 test feedbacks with employee associations.');
    }
}
