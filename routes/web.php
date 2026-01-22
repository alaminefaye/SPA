<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnniversaireController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalonController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PublicReservationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QrScannerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\LoginActivityController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeAttendanceController;
use App\Http\Controllers\LoyaltyPointsController;
use App\Http\Controllers\RappelRendezVousController;

// Welcome page
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes simplifiées pour l'assiduité des employés
Route::prefix('attendance')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\EmployeeAttendanceController::class, 'index'])
        ->middleware('can:view employee attendances')
        ->name('attendance.index');
    Route::get('/calendar', [App\Http\Controllers\Admin\EmployeeAttendanceController::class, 'calendar'])
        ->middleware('can:view employee attendances')
        ->name('attendance.calendar');
    Route::get('/report', [App\Http\Controllers\Admin\EmployeeAttendanceController::class, 'report'])
        ->middleware('can:view attendance reports')
        ->name('attendance.report');
    Route::post('/mark', [App\Http\Controllers\Admin\EmployeeAttendanceController::class, 'markAttendance'])
        ->middleware('can:mark employee attendance')
        ->name('attendance.mark');
    Route::post('/departure', [App\Http\Controllers\Admin\EmployeeAttendanceController::class, 'markDeparture'])
        ->middleware('can:mark employee departure')
        ->name('attendance.departure');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Password reset routes - placeholders for future implementation
    Route::get('/forgot-password', function() {
        return view('auth.login');
    })->name('password.request');
});

// Authenticated routes
// Route pour la recherche de menu
Route::get('/api/search', [SearchController::class, 'search'])->name('api.search');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes pour les logs d'activité
    Route::prefix('activity-logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('activity.index');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('activity.show');
        Route::delete('/{id}', [ActivityLogController::class, 'destroy'])->name('activity.destroy');
        Route::delete('/', [ActivityLogController::class, 'clearAll'])->name('activity.clearAll');
    });
    
    // Routes pour les activités de connexion
    Route::prefix('login-activities')->name('login-activities.')->middleware('can:view login activities')->group(function () {
        Route::get('/', [LoginActivityController::class, 'index'])->name('index');
        Route::get('/{loginActivity}', [LoginActivityController::class, 'show'])->name('show');
        Route::delete('/{loginActivity}', [LoginActivityController::class, 'destroy'])->middleware('can:delete login activities')->name('destroy');
        Route::delete('/', [LoginActivityController::class, 'clearAll'])->middleware('can:delete login activities')->name('clear-all');
    });
    
    // Routes pour la génération de QR code
    Route::prefix('qrcode')->name('qrcode.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\QrCodeController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\Admin\QrCodeController::class, 'generate'])->name('generate');
        Route::get('/download/{filename}', [\App\Http\Controllers\Admin\QrCodeController::class, 'download'])->name('download');
    });
    
    // Salon routes
    Route::resource('salons', SalonController::class);
    
    // Prestation routes
    Route::resource('prestations', PrestationController::class);
    
    // Client routes
    Route::resource('clients', ClientController::class);
    Route::get('/clients-anniversaires', [\App\Http\Controllers\AnniversaireController::class, 'index'])->name('clients.anniversaires');
    Route::get('/debug-anniversaires', [\App\Http\Controllers\DebugController::class, 'debug'])->name('debug.anniversaires');
    
    // Client import routes
    Route::get('/clients-import', [ClientController::class, 'showImportForm'])->name('clients.import-form');
    Route::post('/clients-import', [ClientController::class, 'import'])->name('clients.import');
    Route::get('/clients-template', [ClientController::class, 'downloadTemplate'])->name('clients.download-template');
    Route::get('/clients-export', [ClientController::class, 'export'])->name('clients.export');
    
    // Points de fidélité routes
    Route::group(['prefix' => 'loyalty-points', 'as' => 'loyalty-points.', 'middleware' => ['auth']], function () {
        Route::get('/', [LoyaltyPointsController::class, 'index'])->middleware('can:view loyalty points')->name('index');
        Route::get('/{client}/edit', [LoyaltyPointsController::class, 'edit'])->middleware('can:manage loyalty points')->name('edit');
        Route::put('/{client}', [LoyaltyPointsController::class, 'update'])->middleware('can:manage loyalty points')->name('update');
        Route::get('/{client}/history', [LoyaltyPointsController::class, 'history'])->middleware('can:view loyalty points')->name('history');
    });
    
    // Routes pour le démarrage et la fin de séance
    // Note: Ces routes spécifiques doivent être définies AVANT la route resource
    Route::get('/seances/a-demarrer', [SeanceController::class, 'aDemarrer'])->name('seances.a_demarrer');
    Route::get('/seances/terminees', [SeanceController::class, 'terminees'])->name('seances.terminees');
    
    // Routes pour les rappels de rendez-vous
    Route::prefix('rappels')->name('rappels.')->group(function () {
        Route::get('/', [RappelRendezVousController::class, 'index'])->name('index');
        Route::post('/{rappel}/confirmer', [RappelRendezVousController::class, 'confirmer'])->name('confirmer');
        Route::post('/{rappel}/annuler', [RappelRendezVousController::class, 'annuler'])->name('annuler');
        Route::get('/{rappel}/creer-seance', [RappelRendezVousController::class, 'creerSeance'])->name('creer-seance');
    });
    Route::get('/seances-test/demarrage', [SeanceController::class, 'aDemarrer'])->name('seances.test.demarrage');
    Route::post('/seances/{id}/demarrer', [SeanceController::class, 'demarrer'])->name('seances.demarrer');
    Route::post('/seances/{id}/terminer', [SeanceController::class, 'terminer'])->name('seances.terminer');
    Route::get('/seances/{id}/ticket', [SeanceController::class, 'imprimerTicket'])->name('seances.ticket');
    
    // Seance routes
    Route::resource('seances', SeanceController::class);
    
    // AJAX routes pour les séances
    Route::get('/client-search', [SeanceController::class, 'getClientByPhone'])->name('seances.getClientByPhone');
    Route::get('/prestation-details', [SeanceController::class, 'getPrestationDetails'])->name('seances.getPrestationDetails');
    
    // Reservation routes (admin)
    Route::resource('reservations', ReservationController::class);
    
    // AJAX routes pour les réservations (admin)
    Route::get('/admin/client-search', [ReservationController::class, 'getClientByPhone'])->name('reservations.getClientByPhone');
    Route::post('/admin/prestation-details', [ReservationController::class, 'getPrestationDetails'])->name('reservations.getPrestationDetails');
    
    // Product category routes
    Route::resource('product-categories', ProductCategoryController::class);
    
    // Product routes
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.updateStock');
    
    // Purchase routes
    Route::resource('purchases', PurchaseController::class)->except(['destroy']);
    Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    Route::get('/purchases/{purchase}/ticket', [PurchaseController::class, 'imprimerTicket'])->name('purchases.ticket');
    Route::get('/product-details', [PurchaseController::class, 'getProductDetails'])->name('purchases.getProductDetails');
    
    // Feedback routes (admin)
    Route::resource('feedbacks', FeedbackController::class)->except(['edit', 'update']);
    Route::put('/feedbacks/{feedback}/mark-read', [FeedbackController::class, 'markAsRead'])->name('feedbacks.mark-read');
    Route::put('/feedbacks/{feedback}/toggle-priority', [FeedbackController::class, 'togglePriority'])->name('feedbacks.toggle-priority');
    
    // User management routes
    Route::resource('users', UserController::class);
    
    // Gestion des rôles et permissions
    Route::group(['middleware' => 'auth'], function () {
        // Routes pour les rôles avec middlewares spécifiques
        Route::get('/roles', [RoleController::class, 'index'])->middleware('can:view roles')->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->middleware('can:create roles')->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('can:create roles')->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->middleware('can:view roles')->name('roles.show');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('can:edit roles')->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('can:edit roles')->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('can:delete roles')->name('roles.destroy');
        
        // Routes pour les permissions (lecture seule)
        Route::get('/permissions', [PermissionController::class, 'index'])->middleware('can:view roles')->name('permissions.index');
        Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->middleware('can:view roles')->name('permissions.show');
    });
    
    // QR Code Scanner routes
    Route::get('/qr-scanner', [QrScannerController::class, 'index'])->name('qrscanner.index');
    Route::post('/qr-scanner/process', [QrScannerController::class, 'process'])->name('qrscanner.process');
    
    // Routes pour la gestion des employés
    Route::prefix('admin/employees')->name('admin.employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->middleware('can:view employees')->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->middleware('can:create employees')->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->middleware('can:create employees')->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->middleware('can:view employees')->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->middleware('can:edit employees')->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->middleware('can:edit employees')->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->middleware('can:delete employees')->name('destroy');
        Route::patch('/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->middleware('can:toggle employee status')->name('toggle-status');
        
        // Routes pour la liste de présence des employés sous admin/employees (gardées pour compatibilité)
        Route::prefix('attendance')->name('admin.employees.attendance.')->group(function () {
            Route::get('/', [EmployeeAttendanceController::class, 'index'])
                ->middleware('can:view employee attendances')
                ->name('index');
            Route::get('/calendar', [EmployeeAttendanceController::class, 'calendar'])
                ->middleware('can:view employee attendances')
                ->name('calendar');
            Route::get('/report', [EmployeeAttendanceController::class, 'report'])
                ->middleware('can:view attendance reports')
                ->name('report');
        });
        
        // Les routes d'actions d'assiduité ont été déplacées vers le groupe simplifié 'attendance'
    });

    // Routes pour les notes de satisfaction
    Route::prefix('admin/satisfaction')->name('admin.satisfaction.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SatisfactionRatingController::class, 'index'])->middleware('can:view employees')->name('index');
        Route::get('/top-employees', [\App\Http\Controllers\Admin\SatisfactionRatingController::class, 'topEmployees'])->middleware('can:view employees')->name('top-employees');
    });

    // Client search route
    Route::get('/client-search-by-phone', [ClientController::class, 'searchByPhone'])->name('clients.searchByPhone');
    
    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });
    
    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    });
});

// Routes publiques pour les réservations (sans authentification)
Route::prefix('reservation-publique')->group(function () {
    Route::get('/', [PublicReservationController::class, 'showForm'])->name('reservations.public.form');
    Route::post('/store', [PublicReservationController::class, 'store'])->name('reservations.public.store');
    Route::get('/confirmation', [PublicReservationController::class, 'confirmation'])->name('reservations.public.confirmation');
    Route::post('/prestation-details', [PublicReservationController::class, 'getPrestationDetails'])->name('reservations.public.getPrestationDetails');
});

// Routes publiques pour les suggestions et préoccupations (sans authentification)
Route::prefix('suggestions')->group(function () {
    Route::get('/', [FeedbackController::class, 'showPublicForm'])->name('feedbacks.public.form');
    Route::post('/envoyer', [FeedbackController::class, 'store'])->name('feedbacks.store');
});

// Routes API pour les notifications et alertes
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/seances/en-cours', [SeanceController::class, 'getSeancesEnCours'])->name('api.seances.en-cours');
    Route::get('/seances/{id}/status', [SeanceController::class, 'checkSeanceStatus'])->name('api.seances.check-status');
});

// Routes de test
Route::middleware('auth')->group(function () {
    Route::get('/notifications-test', [SeanceController::class, 'notificationTest'])->name('notifications.test');
});

// Routes pour les rapports
Route::middleware(['auth'])->prefix('admin/reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index')->middleware('can:view reports');
    
    // Rapports de prestations
    Route::get('/prestations', [ReportController::class, 'prestations'])->name('prestations')->middleware('can:view reports');
    Route::get('/prestations/excel', [ReportController::class, 'exportPrestationsExcel'])->name('prestations.excel')->middleware('can:export reports');
    Route::get('/prestations/pdf', [ReportController::class, 'exportPrestationsPdf'])->name('prestations.pdf')->middleware('can:export reports');
    
    // Rapports de produits
    Route::get('/products', [ReportController::class, 'products'])->name('products')->middleware('can:view reports');
    Route::get('/products/excel', [ReportController::class, 'exportProductsExcel'])->name('products.excel')->middleware('can:export reports');
    Route::get('/products/pdf', [ReportController::class, 'exportProductsPdf'])->name('products.pdf')->middleware('can:export reports');
    
    // Rapports de séances
    Route::get('/seances', [ReportController::class, 'seances'])->name('seances')->middleware('can:view reports');
    Route::get('/seances/excel', [ReportController::class, 'exportSeancesExcel'])->name('seances.excel')->middleware('can:export reports');
    Route::get('/seances/pdf', [ReportController::class, 'exportSeancesPdf'])->name('seances.pdf')->middleware('can:export reports');
    
    // Rapports d'achats
    Route::get('/purchases/excel', [ReportController::class, 'exportPurchasesExcel'])->name('purchases.excel')->middleware('can:export reports');
});

// Route temporaire pour réparer les permissions
Route::get('/fix-admin-permissions', function () {
    // Récupérer le super-admin
    $user = \App\Models\User::where('email', 'admin@admin.com')->first();
    
    if (!$user) {
        return 'Utilisateur admin non trouvé';
    }
    
    // Vider le cache des permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    
    // S'assurer que toutes les permissions existent
    $viewReports = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reports']);
    $exportReports = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'export reports']);
    
    // S'assurer que le rôle super-admin existe
    $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin']);
    
    // Donner explicitement les permissions au rôle
    $superAdminRole->givePermissionTo($viewReports);
    $superAdminRole->givePermissionTo($exportReports);
    
    // Vérifier que l'utilisateur a le rôle
    if (!$user->hasRole('super-admin')) {
        $user->assignRole('super-admin');
    }
    
    // Vérifier les permissions
    $hasViewReports = $user->hasPermissionTo('view reports') ? 'Oui' : 'Non';
    $hasExportReports = $user->hasPermissionTo('export reports') ? 'Oui' : 'Non';
    
    return "Permissions réparées. L'utilisateur {$user->name} a-t-il les permissions? <br>" .
           "view reports: {$hasViewReports} <br>" .
           "export reports: {$hasExportReports} <br>" .
           "<a href='/' class='btn btn-primary'>Retour à l'accueil</a>";
});
