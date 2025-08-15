<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

// Welcome page
Route::get('/', function () {
    return redirect()->route('login');
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
    
    // Salon routes
    Route::resource('salons', SalonController::class);
    
    // Prestation routes
    Route::resource('prestations', PrestationController::class);
    
    // Client routes
    Route::resource('clients', ClientController::class);
    
    // Routes pour le démarrage et la fin de séance
    // Note: Ces routes spécifiques doivent être définies AVANT la route resource
    Route::get('/seances/a-demarrer', [SeanceController::class, 'aDemarrer'])->name('seances.a_demarrer');
    Route::get('/seances/terminees', [SeanceController::class, 'terminees'])->name('seances.terminees');
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
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    Route::get('/product-details', [PurchaseController::class, 'getProductDetails'])->name('purchases.getProductDetails');
    
    // Feedback routes (admin)
    Route::resource('feedbacks', FeedbackController::class)->except(['edit', 'update']);
    Route::put('/feedbacks/{feedback}/mark-read', [FeedbackController::class, 'markAsRead'])->name('feedbacks.mark-read');
    Route::put('/feedbacks/{feedback}/toggle-priority', [FeedbackController::class, 'togglePriority'])->name('feedbacks.toggle-priority');
    
    // User management routes
    Route::resource('users', UserController::class);
    
    // QR Code Scanner routes
    Route::get('/qr-scanner', [QrScannerController::class, 'index'])->name('qrscanner.index');
    Route::post('/qr-scanner/process', [QrScannerController::class, 'process'])->name('qrscanner.process');
    
    // Client search route
    Route::get('/client-search-by-phone', [ClientController::class, 'searchByPhone'])->name('clients.searchByPhone');
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
