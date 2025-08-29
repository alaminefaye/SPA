<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\Salon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeAttendanceController extends Controller
{
    /**
     * Constructor pour ajouter les middleware de vérification des permissions
     */
    public function __construct()
    {
        $this->middleware('can:view employee attendances', ['only' => ['index', 'calendar', 'report']]);
        $this->middleware('can:mark employee attendance', ['only' => ['markAttendance']]);
        $this->middleware('can:mark employee departure', ['only' => ['markDeparture']]);
        $this->middleware('can:mark employee absent', ['only' => ['markAttendance']]);
        $this->middleware('can:view attendance reports', ['only' => ['report']]);
    }

    /**
     * Display a listing of employee attendance.
     */
    public function index(Request $request)
    {
        // Par défaut, utiliser la date d'aujourd'hui
        $date = $request->filled('date') ? Carbon::parse($request->date) : now()->startOfDay();
        
        // Récupérer les employés actifs avec leurs enregistrements de présence pour la date sélectionnée
        $employees = Employee::where('actif', true)
            ->with(['salon'])
            ->orderBy('nom')
            ->paginate(10);
            
        // Récupérer les présences pour la date sélectionnée
        $attendances = EmployeeAttendance::where('date', $date->format('Y-m-d'))
            ->get()
            ->keyBy('employee_id');
            
        // Récupérer les salons pour le filtre
        $salons = Salon::orderBy('nom')->pluck('nom', 'id');
        
        // Préserver les paramètres lors de la navigation entre les pages
        $employees->appends(['date' => $date->format('Y-m-d')]);
        
        return view('admin.employees.attendance.index', compact('employees', 'attendances', 'date', 'salons'));
    }
    
    /**
     * Affiche une vue calendaire des présences pour un mois donné
     */
    public function calendar(Request $request)
    {
        $month = $request->filled('month') ? Carbon::parse($request->month) : now()->startOfMonth();
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();
        
        // Récupérer les employés actifs
        $employees = Employee::where('actif', true)
            ->orderBy('nom')
            ->get();
            
        // Récupérer toutes les présences pour le mois
        $attendances = EmployeeAttendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy(['employee_id', function($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            }]);
        
        // Générer le calendrier du mois
        $calendar = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $calendar[] = $currentDate->copy();
            $currentDate->addDay();
        }
        
        return view('admin.employees.attendance.calendar', compact('employees', 'attendances', 'calendar', 'month'));
    }
    
    /**
     * Marque la présence d'un employé
     */
    public function markAttendance(Request $request)
    {
        // Log complet des données reçues pour déboguer
        \Log::info('Données de présence reçues:', [
            'all_data' => $request->all(),
            'is_ajax' => $request->ajax(),
            'method' => $request->method(),
            'url' => $request->url(),
        ]);
        
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'present' => 'required|boolean',
                'arrival_time' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);
            
            // Log des données validées
            \Log::info('Données validées:', $validated);
            
            // Recherche ou création d'un enregistrement de présence
            $attendance = EmployeeAttendance::updateOrCreate(
                ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
                [
                    'present' => $validated['present'],
                    'arrival_time' => $validated['arrival_time'] ?? null,
                    'notes' => $validated['notes'] ?? null
                ]
            );
            
            \Log::info('Présence enregistrée:', ['attendance' => $attendance]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'attendance' => $attendance
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement de la présence:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'Présence enregistrée avec succès');
    }
    
    /**
     * Met à jour l'heure de départ d'un employé
     */
    public function markDeparture(Request $request)
    {
        // Log complet des données reçues pour déboguer
        \Log::info('Données de départ reçues:', [
            'all_data' => $request->all(),
            'is_ajax' => $request->ajax(),
            'method' => $request->method(),
            'url' => $request->url(),
        ]);
        
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'departure_time' => 'required|string',
            ]);
            
            // Log des données validées
            \Log::info('Données de départ validées:', $validated);
            
            $attendance = EmployeeAttendance::where([
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date']
            ])->first();
            
            if (!$attendance) {
                \Log::warning('Enregistrement de présence non trouvé pour marquer le départ:', $validated);
                
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Enregistrement de présence non trouvé'], 404);
                }
                return redirect()->back()->with('error', 'Enregistrement de présence non trouvé');
            }
            
            $attendance->update([
                'departure_time' => $validated['departure_time']
            ]);
            
            \Log::info('Départ enregistré:', ['attendance' => $attendance]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'attendance' => $attendance
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement du départ:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'Heure de départ enregistrée avec succès');
    }
    
    /**
     * Générer un rapport de présence
     */
    public function report(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();
        $employeeId = $request->filled('employee_id') ? $request->employee_id : null;
        
        $query = EmployeeAttendance::with('employee')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        $attendances = $query->orderBy('date', 'desc')->get();
        
        // Statistiques de présence
        $stats = [
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'present_count' => $attendances->where('present', true)->count(),
            'absent_count' => $attendances->where('present', false)->count(),
        ];
        
        // Récupérer les employés pour le filtre
        $employees = Employee::where('actif', true)->orderBy('nom')->pluck('nom', 'id');
        
        return view('admin.employees.attendance.report', compact(
            'attendances', 'startDate', 'endDate', 'employeeId', 'employees', 'stats'
        ));
    }
}
