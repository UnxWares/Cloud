<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServicePackController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\DeploymentController;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

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

// API Routes - Proxy vers l'orchestrateur
Route::prefix('api')->group(function () {
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/token-dev', [AuthController::class, 'generateDevToken']); // Dev/Test uniquement
        Route::post('/login', [AuthController::class, 'login']); // Production avec OIDC
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/status', [AuthController::class, 'status']);
    });

    // Service Packs (/v1/products dans l'orchestrateur)
    Route::get('/service-packs', [ServicePackController::class, 'index']);
    Route::get('/service-packs/{id}', [ServicePackController::class, 'show']);

    // Services (dans les service packs)
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);

    // Deployments (instances déployées)
    Route::get('/deployments', [DeploymentController::class, 'index']);
    Route::get('/deployments/{id}', [DeploymentController::class, 'show']);
});
