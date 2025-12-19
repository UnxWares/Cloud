<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrchestratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * Génère un token JWT de test (dev uniquement)
     * POST /api/auth/token-dev
     */
    public function generateDevToken(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|uuid'
        ]);

        try {
            $result = $this->orchestrator->generateToken($request->customer_id);

            return response()->json([
                'success' => true,
                'message' => 'Token généré et stocké en session',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Échec de génération du token',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login avec OIDC (production - à implémenter)
     * POST /api/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'oidc_token' => 'required|string'
        ]);

        try {
            $result = $this->orchestrator->loginWithOidc($request->oidc_token);

            return response()->json([
                'success' => true,
                'message' => 'Authentification réussie',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Échec de l\'authentification',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Déconnexion
     * POST /api/auth/logout
     */
    public function logout(): JsonResponse
    {
        $this->orchestrator->logout();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Vérifier le statut d'authentification
     * GET /api/auth/status
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'authenticated' => $this->orchestrator->isAuthenticated(),
            'token' => $this->orchestrator->getJwtToken()
        ]);
    }
}
