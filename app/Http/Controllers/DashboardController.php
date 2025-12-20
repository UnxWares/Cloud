<?php

namespace App\Http\Controllers;

use App\Services\OrchestratorService;
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

        // Tenter de charger les déploiements du customer (nécessite JWT)
        try {
            $deployments = $this->orchestrator->getCustomerDeployments();

            // Transformer les déploiements de l'API au format attendu par le frontend
            $deployments = array_map(function ($deployment) {
                return [
                    'id' => $deployment['id'],
                    'name' => $deployment['attributes']['name'] ?? 'Sans nom',
                    'service_name' => $deployment['service'] ?? null,
                    'service_pack_id' => $deployment['pack'] ?? null,
                    'status' => strtolower($deployment['status'] ?? 'pending'),
                    'datacenter' => $deployment['infra']['datacenter'] ?? $deployment['datacenter'] ?? 'Paris', // Datacenter par défaut
                    'ip' => $deployment['infra']['ip'] ?? $deployment['ip'] ?? null,
                    'specs' => $deployment['performances'] ?? [],
                    'created_at' => $deployment['created_at'] ?? null,
                ];
            }, $deployments);
        } catch (\Exception $e) {
            // Si pas de token JWT ou erreur, utiliser les données de démo
            $deployments = [
                // France
                [
                    'id' => '1',
                    'name' => 'Web App Production',
                    'service_name' => 'VPS',
                    'service_pack_id' => 'compute',
                    'status' => 'running',
                    'datacenter' => 'Paris',
                    'ip' => '51.15.228.45',
                    'specs' => ['cpu' => '4', 'ram' => '8', 'storage' => '200']
                ],
                [
                    'id' => '2',
                    'name' => 'Database Primary',
                    'service_name' => 'PostgreSQL',
                    'service_pack_id' => 'database',
                    'status' => 'running',
                    'datacenter' => 'Paris',
                    'ip' => '51.15.228.46',
                    'specs' => ['cpu' => '8', 'ram' => '16', 'storage' => '500']
                ],
                [
                    'id' => '3',
                    'name' => 'Cache Server',
                    'service_name' => 'Redis',
                    'service_pack_id' => 'database',
                    'status' => 'running',
                    'datacenter' => 'Lyon',
                    'ip' => '51.15.229.12',
                    'specs' => ['cpu' => '2', 'ram' => '4', 'storage' => '50']
                ],
                // Allemagne
                [
                    'id' => '4',
                    'name' => 'API Gateway',
                    'service_name' => 'VPS',
                    'service_pack_id' => 'compute',
                    'status' => 'running',
                    'datacenter' => 'Frankfurt',
                    'ip' => '46.4.123.78',
                    'specs' => ['cpu' => '4', 'ram' => '8', 'storage' => '100']
                ],
                [
                    'id' => '5',
                    'name' => 'ML Processing',
                    'service_name' => 'VPS',
                    'service_pack_id' => 'compute',
                    'status' => 'running',
                    'datacenter' => 'Munich',
                    'ip' => '46.4.124.91',
                    'specs' => ['cpu' => '16', 'ram' => '32', 'storage' => '1000']
                ],
                // UK
                [
                    'id' => '6',
                    'name' => 'CDN Edge',
                    'service_name' => 'VPS',
                    'service_pack_id' => 'network',
                    'status' => 'running',
                    'datacenter' => 'London',
                    'ip' => '185.12.45.67',
                    'specs' => ['cpu' => '2', 'ram' => '4', 'storage' => '100']
                ],
                // Pays-Bas
                [
                    'id' => '7',
                    'name' => 'Storage Cluster',
                    'service_name' => 'S3',
                    'service_pack_id' => 'storage',
                    'status' => 'running',
                    'datacenter' => 'Amsterdam',
                    'ip' => '95.211.178.34',
                    'specs' => ['storage' => '5000']
                ],
                [
                    'id' => '8',
                    'name' => 'Backup Server',
                    'service_name' => 'S3',
                    'service_pack_id' => 'storage',
                    'status' => 'running',
                    'datacenter' => 'Amsterdam',
                    'ip' => '95.211.178.35',
                    'specs' => ['storage' => '10000']
                ],
                // Espagne
                [
                    'id' => '9',
                    'name' => 'Analytics DB',
                    'service_name' => 'MySQL',
                    'service_pack_id' => 'database',
                    'status' => 'running',
                    'datacenter' => 'Madrid',
                    'ip' => '185.44.77.123',
                    'specs' => ['cpu' => '4', 'ram' => '16', 'storage' => '500']
                ],
                // Italie
                [
                    'id' => '10',
                    'name' => 'Media Server',
                    'service_name' => 'VPS',
                    'service_pack_id' => 'compute',
                    'status' => 'running',
                    'datacenter' => 'Milan',
                    'ip' => '217.64.33.89',
                    'specs' => ['cpu' => '8', 'ram' => '16', 'storage' => '2000']
                ],
                // Nord
                [
                    'id' => '11',
                    'name' => 'Log Aggregator',
                    'service_name' => 'VPS',
                    'service_pack_id' => 'compute',
                    'status' => 'running',
                    'datacenter' => 'Stockholm',
                    'ip' => '46.239.107.54',
                    'specs' => ['cpu' => '4', 'ram' => '8', 'storage' => '500']
                ],
                // Serveurs en maintenance/pending
                [
                    'id' => '12',
                    'name' => 'Test Environment',
                    'service_name' => 'VPS',
                    'service_pack_id' => 'compute',
                    'status' => 'pending',
                    'datacenter' => 'Frankfurt',
                    'ip' => null,
                    'specs' => ['cpu' => '2', 'ram' => '4', 'storage' => '50']
                ],
            ];
        }

        return Inertia::render('Dashboard', [
            'deployments' => $deployments,
        ]);
    }

    /**
     * Affiche la page d'un service spécifique avec ses offres
     */
    public function showService(string $packSlug, string $serviceSlug): Response
    {
        try {
            // Charger les offres du service
            $offers = $this->orchestrator->getServiceOffers($packSlug, $serviceSlug);

            // Charger les déploiements du service (si authentifié)
            try {
                $deployments = $this->orchestrator->getServiceDeployments($packSlug, $serviceSlug);

                // Transformer les déploiements
                $deployments = array_map(function ($deployment) {
                    return [
                        'id' => $deployment['id'],
                        'name' => $deployment['attributes']['name'] ?? 'Sans nom',
                        'offer' => $deployment['offer'] ?? null,
                        'status' => strtolower($deployment['status'] ?? 'pending'),
                        'ip' => $deployment['ip'] ?? null,
                        'created_at' => $deployment['created_at'] ?? null,
                    ];
                }, $deployments);
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
            // Rediriger vers le dashboard si erreur
            return redirect('/dashboard')
                ->with('error', "Service non trouvé : {$e->getMessage()}");
        }
    }
}
