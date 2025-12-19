<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrchestratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * GET /api/services?pack_slug=xxx&service_slug=xxx
     * Liste les offres d'un service
     */
    public function index(Request $request): JsonResponse
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

            $offers = $this->orchestrator->getServiceOffers($packSlug, $serviceSlug);
            return response()->json($offers);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch service offers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/services/{offer_id}?pack_slug=xxx&service_slug=xxx
     * Détails d'une offre spécifique
     */
    public function show(Request $request, string $offerId): JsonResponse
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

            $offer = $this->orchestrator->getOfferDetails($packSlug, $serviceSlug, $offerId);
            return response()->json($offer);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch offer details',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
