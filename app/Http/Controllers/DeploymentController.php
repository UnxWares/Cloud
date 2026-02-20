<?php

namespace App\Http\Controllers;

use App\Services\OrchestratorService;
use App\Http\Requests\StoreDeploymentRequest;
use Illuminate\Http\RedirectResponse;

class DeploymentController extends Controller
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * Crée un nouveau déploiement
     */
    public function store(StoreDeploymentRequest $request, string $packSlug, string $serviceSlug): RedirectResponse
    {
        try {
            // Ajouter pack_slug et service_slug aux données validées
            $data = array_merge($request->validated(), [
                'pack_slug' => $packSlug,
                'service_slug' => $serviceSlug,
            ]);

            $this->orchestrator->createDeployment($data);

            return redirect()
                ->route('dashboard.service', ['pack_slug' => $packSlug, 'service_slug' => $serviceSlug])
                ->with('success', "Déploiement '{$request->name}' créé avec succès");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', "Erreur lors de la création : {$e->getMessage()}");
        }
    }

    /**
     * Supprime un déploiement
     */
    public function destroy(string $packSlug, string $serviceSlug, string $deploymentId): RedirectResponse
    {
        try {
            $this->orchestrator->deleteDeployment($packSlug, $serviceSlug, $deploymentId);

            return back()->with('success', 'Déploiement supprimé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de la suppression : {$e->getMessage()}");
        }
    }

    /**
     * Suspend un déploiement
     */
    public function suspend(string $packSlug, string $serviceSlug, string $deploymentId): RedirectResponse
    {
        try {
            $this->orchestrator->suspendDeployment($packSlug, $serviceSlug, $deploymentId);

            return back()->with('success', 'Déploiement suspendu avec succès');
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de la suspension : {$e->getMessage()}");
        }
    }

    /**
     * Réactive un déploiement
     */
    public function resume(string $packSlug, string $serviceSlug, string $deploymentId): RedirectResponse
    {
        try {
            $this->orchestrator->resumeDeployment($packSlug, $serviceSlug, $deploymentId);

            return back()->with('success', 'Déploiement réactivé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de la réactivation : {$e->getMessage()}");
        }
    }
}
