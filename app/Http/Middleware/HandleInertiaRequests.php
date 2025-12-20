<?php

namespace App\Http\Middleware;

use App\Services\OrchestratorService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Charger les service packs avec leurs services
        $servicePacks = $this->loadServicePacks();

        return [
            ...parent::share($request),
            'servicePacks' => $servicePacks,
        ];
    }

    /**
     * Charge les service packs depuis l'orchestrateur
     */
    private function loadServicePacks(): array
    {
        try {
            $servicePacks = $this->orchestrator->getServicePacks();

            // Pour chaque pack, charger ses services
            foreach ($servicePacks as &$pack) {
                try {
                    $packSlug = $pack['slug'] ?? null;
                    if ($packSlug) {
                        $services = $this->orchestrator->getPackServices($packSlug);
                        $pack['services'] = $services;
                    } else {
                        $pack['services'] = [];
                    }
                } catch (\Exception $e) {
                    $pack['services'] = [];
                }
            }

            return $servicePacks;
        } catch (\Exception $e) {
            // Données de démo si l'API échoue
            return [
                ['id' => 'storage', 'slug' => 'storage', 'name' => 'Storage', 'icon' => 'HardDrive', 'description' => 'Services de stockage', 'services' => []],
                ['id' => 'compute', 'slug' => 'compute', 'name' => 'Compute', 'icon' => 'Server', 'description' => 'Serveurs et VM', 'services' => []],
                ['id' => 'network', 'slug' => 'network', 'name' => 'Network', 'icon' => 'Globe', 'description' => 'Services réseau', 'services' => []]
            ];
        }
    }
}
