<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrchestratorService;
use Illuminate\Http\JsonResponse;

class ServicePackController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * GET /api/service-packs
     * Liste tous les packs de services
     */
    public function index(): JsonResponse
    {
        try {
            $servicePacks = $this->orchestrator->getServicePacks();
            return response()->json($servicePacks);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch service packs',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/service-packs/{pack_slug}
     * Liste les services d'un pack spécifique
     */
    public function show(string $packSlug): JsonResponse
    {
        try {
            $services = $this->orchestrator->getPackServices($packSlug);
            return response()->json($services);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch pack services',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
