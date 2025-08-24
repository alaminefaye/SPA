<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Illuminate\Database\Seeder;

class FeedbackSatisfactionSeeder extends Seeder
{
    /**
     * Run the seeder to update feedback entries with sample satisfaction ratings.
     *
     * @return void
     */
    public function run()
    {
        // Get all feedbacks that don't have satisfaction ratings
        $feedbacks = Feedback::whereNull('satisfaction_rating')->get();
        
        foreach ($feedbacks as $feedback) {
            // Assign a random rating between 1-5
            $feedback->satisfaction_rating = rand(1, 5);
            $feedback->save();
            
            $this->command->info("Feedback #{$feedback->id} updated with satisfaction rating: {$feedback->satisfaction_rating}");
        }
        
        $this->command->info("Total feedbacks updated: " . $feedbacks->count());
    }
}
