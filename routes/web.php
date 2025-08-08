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
    
    // Salon routes
    Route::resource('salons', SalonController::class);
    
    // Prestation routes
    Route::resource('prestations', PrestationController::class);
    
    // Client routes
    Route::resource('clients', ClientController::class);
    
    // Seance routes
    Route::resource('seances', SeanceController::class);
    
    // AJAX routes pour les séances
    Route::get('/client-search', [SeanceController::class, 'getClientByPhone'])->name('seances.getClientByPhone');
    Route::get('/prestation-details', [SeanceController::class, 'getPrestationDetails'])->name('seances.getPrestationDetails');
    
    // Routes pour le démarrage et la fin de séance
    Route::post('/seances/{id}/demarrer', [SeanceController::class, 'demarrer'])->name('seances.demarrer');
    Route::post('/seances/{id}/terminer', [SeanceController::class, 'terminer'])->name('seances.terminer');
    
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
