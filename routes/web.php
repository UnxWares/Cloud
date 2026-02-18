<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

// Health check route for monitoring
Route::get('/health', function () {
    return response()->json(['status' => 'OK'], 200);
});

// Page d'accueil - www.unxwares.cloud (en dev: localhost:8000)
Route::get('/', function () {
    return Inertia::render('Home');
});

// Dashboard - dashboard.unxwares.cloud (en dev: localhost:8000/dashboard)
Route::get('/dashboard', [DashboardController::class, 'index']);

// Page d'un service spécifique avec ses offres
Route::get('/dashboard/{pack_slug}/{service_slug}', [DashboardController::class, 'showService']);

// DEV ONLY - Route de test pour générer un token facilement depuis le navigateur
// À SUPPRIMER en production !
Route::get('/dev/auth-test', function () {
    if (app()->environment('production')) {
        abort(404);
    }

    $orchestrator = app(\App\Services\OrchestratorService::class);

    // Customer ID par défaut pour les tests (UUID 0)
    $customerId = request('customer_id', '00000000-0000-0000-0000-000000000000');

    try {
        $result = $orchestrator->generateToken($customerId);

        return redirect('/dashboard')->with('success', "Token généré ! Customer: {$result['customer_id']}");
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Échec de génération du token',
            'message' => $e->getMessage(),
            'tip' => 'Vérifie que l\'orchestrateur Go tourne sur ' . config('services.orchestrator.url')
        ], 500);
    }
});

// API Routes - Authentication uniquement
Route::prefix('api')->group(function () {
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/token-dev', [AuthController::class, 'generateDevToken']); // Dev/Test uniquement
        Route::post('/login', [AuthController::class, 'login']); // Production avec OIDC
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/status', [AuthController::class, 'status']);
    });
});
