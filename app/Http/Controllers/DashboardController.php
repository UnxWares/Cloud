<?php

namespace App\Http\Controllers;

use App\Services\OrchestratorService;
use App\Http\Resources\DeploymentResource;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    public function index(): Response
    {
        // Les servicePacks sont maintenant chargés dans le middleware HandleInertiaRequests
        // L'authentification est vérifiée par le middleware EnsureOrchestratorAuthenticated

        try {
            $deploymentsData = $this->orchestrator->getCustomerDeployments();
            $deployments = DeploymentResource::collection($deploymentsData)->resolve();

            return Inertia::render('Dashboard', [
                'deployments' => $deployments,
            ]);
        } catch (\Exception $e) {
            // Afficher la page Orchestrateur indisponible
            return Inertia::render('OrchestratorUnavailable', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Affiche la page d'un service spécifique avec ses offres
     */
    public function showService(string $packSlug, string $serviceSlug): Response
    {
        // L'authentification est vérifiée par le middleware EnsureOrchestratorAuthenticated

        try {
            // Charger les offres du service
            $offers = $this->orchestrator->getServiceOffers($packSlug, $serviceSlug);

            try {
                $deploymentsData = $this->orchestrator->getServiceDeployments($packSlug, $serviceSlug);
                $deployments = DeploymentResource::collection($deploymentsData)->resolve();
            } catch (\Exception $e) {
                $deployments = [];
            }

            return Inertia::render('ServiceDetails', [
                'packSlug' => $packSlug,
                'serviceSlug' => $serviceSlug,
                'offers' => $offers,
                'deployments' => $deployments,
            ]);
        } catch (\Exception $e) {
            // Afficher la page Orchestrateur indisponible
            return Inertia::render('OrchestratorUnavailable', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Redirige vers unxwares.cloud pour créer un nouveau déploiement
     */
    public function createDeployment(string $packSlug, string $serviceSlug)
    {
        // URL de retour après la création du déploiement
        $returnUrl = urlencode(url()->previous());

        // Redirection vers unxwares.cloud
        return redirect("https://unxwares.cloud/products/{$packSlug}/{$serviceSlug}?return={$returnUrl}");
    }
}
