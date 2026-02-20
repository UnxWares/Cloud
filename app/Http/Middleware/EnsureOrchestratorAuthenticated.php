<?php

namespace App\Http\Middleware;

use App\Services\OrchestratorService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrchestratorAuthenticated
{
    public function __construct(
        private OrchestratorService $orchestrator
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|InertiaResponse
    {
        // Vérifier si l'utilisateur est authentifié via l'orchestrateur
        if (!$this->orchestrator->isAuthenticated()) {
            // Afficher la page Unauthenticated via Inertia
            return Inertia::render('Unauthenticated');
        }

        return $next($request);
    }
}
