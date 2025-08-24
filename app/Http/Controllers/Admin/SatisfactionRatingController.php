<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class SatisfactionRatingController extends Controller
{
    /**
     * Affiche la page d'index des notes de satisfaction par employé
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer tous les employés avec leurs notes de satisfaction
        $employees = Employee::leftJoin('feedbacks', 'employees.id', '=', 'feedbacks.employee_id')
            ->select(
                'employees.id',
                'employees.nom',
                'employees.prenom',
                'employees.photo',
                'employees.actif',
                DB::raw('COUNT(feedbacks.id) as total_feedbacks'),
                DB::raw('COUNT(CASE WHEN feedbacks.satisfaction_rating IS NOT NULL THEN 1 END) as rated_feedbacks'),
                DB::raw('AVG(feedbacks.satisfaction_rating) as avg_rating'),
                DB::raw('COUNT(CASE WHEN feedbacks.satisfaction_rating = 5 THEN 1 END) as five_stars'),
                DB::raw('COUNT(CASE WHEN feedbacks.satisfaction_rating = 4 THEN 1 END) as four_stars'),
                DB::raw('COUNT(CASE WHEN feedbacks.satisfaction_rating = 3 THEN 1 END) as three_stars'),
                DB::raw('COUNT(CASE WHEN feedbacks.satisfaction_rating = 2 THEN 1 END) as two_stars'),
                DB::raw('COUNT(CASE WHEN feedbacks.satisfaction_rating = 1 THEN 1 END) as one_star')
            )
            ->groupBy('employees.id', 'employees.nom', 'employees.prenom', 'employees.photo', 'employees.actif')
            ->orderBy('avg_rating', 'desc')
            ->get();

        // Top 3 des employés les mieux notés
        $topEmployees = $employees->where('rated_feedbacks', '>', 0)
                                 ->take(3);

        return view('admin.satisfaction.index', compact('employees', 'topEmployees'));
    }

    /**
     * Affiche la page des meilleurs employés
     *
     * @return \Illuminate\View\View
     */
    public function topEmployees()
    {
        // Récupérer le top 10 des employés avec les meilleures notes
        $topEmployees = Employee::leftJoin('feedbacks', 'employees.id', '=', 'feedbacks.employee_id')
            ->select(
                'employees.id',
                'employees.nom',
                'employees.prenom',
                'employees.photo',
                DB::raw('COUNT(feedbacks.id) as total_feedbacks'),
                DB::raw('AVG(feedbacks.satisfaction_rating) as avg_rating'),
                DB::raw('COUNT(CASE WHEN feedbacks.satisfaction_rating = 5 THEN 1 END) as five_stars')
            )
            ->groupBy('employees.id', 'employees.nom', 'employees.prenom', 'employees.photo')
            ->having('total_feedbacks', '>', 0)
            ->orderBy('avg_rating', 'desc')
            ->orderBy('five_stars', 'desc')
            ->take(10)
            ->get();

        // Récupérer les 3 derniers mois de notes
        $latestMonths = Feedback::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('satisfaction_rating')
            ->whereNotNull('employee_id')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(3)
            ->get();

        // Pour chaque mois, récupérer l'employé le mieux noté (employé du mois)
        $employeesOfTheMonth = [];
        foreach ($latestMonths as $month) {
            $employeeOfTheMonth = Employee::leftJoin('feedbacks', 'employees.id', '=', 'feedbacks.employee_id')
                ->select(
                    'employees.id',
                    'employees.nom',
                    'employees.prenom',
                    'employees.photo',
                    DB::raw('COUNT(feedbacks.id) as total_feedbacks'),
                    DB::raw('AVG(feedbacks.satisfaction_rating) as avg_rating')
                )
                ->whereRaw('YEAR(feedbacks.created_at) = ?', [$month->year])
                ->whereRaw('MONTH(feedbacks.created_at) = ?', [$month->month])
                ->whereNotNull('feedbacks.satisfaction_rating')
                ->groupBy('employees.id', 'employees.nom', 'employees.prenom', 'employees.photo')
                ->having('total_feedbacks', '>', 0)
                ->orderBy('avg_rating', 'desc')
                ->orderBy('total_feedbacks', 'desc')
                ->first();
            
            if ($employeeOfTheMonth) {
                $employeesOfTheMonth[] = [
                    'employee' => $employeeOfTheMonth,
                    'month' => $month->month,
                    'year' => $month->year,
                    'month_name' => $this->getMonthName($month->month)
                ];
            }
        }

        return view('admin.satisfaction.top', compact('topEmployees', 'employeesOfTheMonth'));
    }

    /**
     * Retourne le nom du mois en français
     *
     * @param int $monthNum
     * @return string
     */
    private function getMonthName($monthNum)
    {
        $months = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];
        
        return $months[$monthNum] ?? 'Inconnu';
    }
}
