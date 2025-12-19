<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrchestratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeploymentController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * GET /api/deployments
     * ou GET /api/deployments?pack_slug=xxx&service_slug=xxx
     * Liste les déploiements du customer (nécessite JWT)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $packSlug = $request->query('pack_slug');
            $serviceSlug = $request->query('service_slug');

            // Si pack et service spécifiés, récupérer les déploiements filtrés
            if ($packSlug && $serviceSlug) {
                $deployments = $this->orchestrator->getServiceDeployments($packSlug, $serviceSlug);
            } else {
                // Sinon, tous les déploiements du customer
                $deployments = $this->orchestrator->getCustomerDeployments();
            }

            return response()->json($deployments);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch deployments',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/deployments/{deployment_id}?pack_slug=xxx&service_slug=xxx
     * Détails complets d'un déploiement (nécessite JWT)
     */
    public function show(Request $request, string $deploymentId): JsonResponse
    {
        try {
            $packSlug = $request->query('pack_slug');
            $serviceSlug = $request->query('service_slug');

            if (!$packSlug || !$serviceSlug) {
                return response()->json([
                    'error' => 'Missing parameters',
                    'message' => 'pack_slug and service_slug are required'
                ], 400);
            }

            $deployment = $this->orchestrator->getDeploymentDetails($packSlug, $serviceSlug, $deploymentId);
            return response()->json($deployment);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch deployment',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
