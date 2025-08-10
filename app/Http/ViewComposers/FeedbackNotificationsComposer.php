<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Feedback;

class FeedbackNotificationsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Compte uniquement les suggestions/préoccupations non lues
        $newFeedbacksCount = Feedback::where('is_read', false)->count();
        
        // Compte les suggestions/préoccupations prioritaires non lues
        $priorityFeedbacksCount = Feedback::where('is_read', false)
                                ->where('is_priority', true)
                                ->count();
        
        // Partage les variables avec la vue
        $view->with([
            'newFeedbacksCount' => $newFeedbacksCount,
            'priorityFeedbacksCount' => $priorityFeedbacksCount
        ]);
    }
}
