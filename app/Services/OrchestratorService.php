<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrchestratorService
{
    private string $baseUrl;
    private ?string $jwtToken;

    public function __construct()
    {
        $this->baseUrl = config('services.orchestrator.url');
        $this->jwtToken = Session::get('orchestrator_jwt_token');
    }

    /**
     * Définir le token JWT pour les requêtes authentifiées
     */
    public function setJwtToken(?string $token): void
    {
        $this->jwtToken = $token;
        if ($token) {
            Session::put('orchestrator_jwt_token', $token);
        } else {
            Session::forget('orchestrator_jwt_token');
        }
    }

    /**
     * Récupérer le token JWT actuel
     */
    public function getJwtToken(): ?string
    {
        return $this->jwtToken;
    }

    private function request(string $method, string $endpoint, array $data = [], bool $authenticated = false)
    {
        try {
            $url = $this->baseUrl . $endpoint;

            $http = Http::asJson();

            // Ajouter le token JWT si la requête nécessite l'authentification
            if ($authenticated && $this->jwtToken) {
                $http = $http->withToken($this->jwtToken);
            }

            $response = $method === 'get'
                ? $http->get($url, $data)
                : $http->$method($url, $data);

            if (!$response->successful()) {
                Log::error('Orchestrator API error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                throw new \Exception("Orchestrator API error: {$response->status()}");
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to call Orchestrator API', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    // ========================================
    // CATALOGUE - Routes publiques
    // ========================================

    /**
     * Lister tous les packs de services
     * GET /v1/products
     */
    public function getServicePacks(): array
    {
        $result = $this->request('get', '/v1/products');
        return $result ?? [];
    }

    /**
     * Lister les services d'un pack
     * GET /v1/products/{pack_slug}
     */
    public function getPackServices(string $packSlug): array
    {
        $result = $this->request('get', "/v1/products/{$packSlug}");
        return $result ?? [];
    }

    /**
     * Lister les offres d'un service
     * GET /v1/products/{pack_slug}/{service_slug}
     */
    public function getServiceOffers(string $packSlug, string $serviceSlug): array
    {
        $result = $this->request('get', "/v1/products/{$packSlug}/{$serviceSlug}");
        return $result ?? [];
    }

    /**
     * Détails d'une offre spécifique
     * GET /v1/products/{pack_slug}/{service_slug}/{offer_id}
     */
    public function getOfferDetails(string $packSlug, string $serviceSlug, string $offerId): array
    {
        $result = $this->request('get', "/v1/products/{$packSlug}/{$serviceSlug}/{$offerId}");
        return $result ?? [];
    }

    // ========================================
    // Anciennes méthodes (compatibilité)
    // À supprimer après migration complète
    // ========================================

    /** @deprecated Utiliser getPackServices() à la place */
    public function getServices(string $packSlug = null)
    {
        if ($packSlug) {
            return $this->getPackServices($packSlug);
        }
        // Fallback: retourner tous les packs
        return $this->getServicePacks();
    }

    /** @deprecated */
    public function getServicePack(string $packSlug)
    {
        return $this->getPackServices($packSlug);
    }

    /** @deprecated */
    public function getService(string $id)
    {
        // Cette méthode ne correspond plus à une route existante
        // À supprimer après migration
        return [];
    }

    /** @deprecated */
    public function getDeployments(string $serviceId = null)
    {
        // Utiliser getCustomerDeployments() à la place
        return $this->getCustomerDeployments();
    }

    /** @deprecated */
    public function getDeployment(string $id)
    {
        // Cette méthode ne correspond plus à une route existante
        return [];
    }

    // ========================================
    // Authentification
    // ========================================

    /**
     * Génère un token JWT pour un customer (dev/test)
     * Retourne ['token' => '...', 'customer_id' => '...', 'expires_at' => '...']
     */
    public function generateToken(string $customerId): array
    {
        $response = $this->request('post', '/v1/auth/token', [
            'customer_id' => $customerId
        ]);

        // Stocker automatiquement le token
        if (isset($response['token'])) {
            $this->setJwtToken($response['token']);
        }

        return $response;
    }

    /**
     * Échange un token OIDC contre un JWT orchestrateur (production)
     * Retourne ['token' => '...', 'customer_id' => '...', 'expires_at' => '...']
     */
    public function loginWithOidc(string $oidcToken): array
    {
        $response = $this->request('post', '/v1/auth/login', [
            'oidc_token' => $oidcToken
        ]);

        // Stocker automatiquement le token
        if (isset($response['token'])) {
            $this->setJwtToken($response['token']);
        }

        return $response;
    }

    /**
     * Déconnexion (supprime le token de la session)
     */
    public function logout(): void
    {
        $this->setJwtToken(null);
    }

    /**
     * Vérifie si l'utilisateur est authentifié (a un token)
     */
    public function isAuthenticated(): bool
    {
        return $this->jwtToken !== null;
    }

    // ========================================
    // Nouvelles routes avec authentification JWT
    // ========================================

    /**
     * Liste tous les déploiements du customer authentifié
     * Nécessite JWT token
     */
    public function getCustomerDeployments(): array
    {
        $result = $this->request('get', '/v1/manage/deployments', [], true);
        return $result ?? [];
    }

    /**
     * Liste les déploiements d'un service spécifique pour le customer authentifié
     * Nécessite JWT token
     */
    public function getServiceDeployments(string $packSlug, string $serviceSlug): array
    {
        $result = $this->request('get', "/v1/manage/{$packSlug}/{$serviceSlug}", [], true);
        return $result ?? [];
    }

    /**
     * Récupère les détails complets d'un déploiement spécifique
     * Nécessite JWT token
     */
    public function getDeploymentDetails(string $packSlug, string $serviceSlug, string $deploymentId): array
    {
        $result = $this->request('get', "/v1/manage/{$packSlug}/{$serviceSlug}/{$deploymentId}", [], true);
        return $result ?? [];
    }
}
