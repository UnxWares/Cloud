<script>
  import { Search } from 'lucide-svelte'
  import DashboardLayout from '../layouts/DashboardLayout.svelte'
  import DeploymentCard from '../components/DeploymentCard.svelte'
  import DatacenterMap from '../components/DatacenterMap.svelte'
  import ServicePackSelector from '../components/ServicePackSelector.svelte'

  // Props depuis Inertia (passées depuis Laravel)
  let { servicePacks = [], deployments = [] } = $props()

  // Filtres locaux
  let selectedPack = $state(null)
  let selectedService = $state('all')
  let searchQuery = $state('')
  let statusFilter = $state('all')
  let datacenterFilter = $state('all')

  // Services uniques (pour le filtre)
  let services = $derived.by(() => {
    const serviceMap = new Map()
    deployments.forEach(d => {
      if (d.service_id && d.service_name) {
        serviceMap.set(d.service_id, {
          id: d.service_id,
          name: d.service_name,
          pack_id: d.service_pack_id
        })
      }
    })
    return Array.from(serviceMap.values())
  })

  // Services filtrés par pack sélectionné
  let availableServices = $derived.by(() => {
    if (!selectedPack) return services
    return services.filter(s => s.pack_id === selectedPack.id)
  })

  // Réinitialiser le filtre service si on change de pack
  $effect(() => {
    if (selectedPack && selectedService !== 'all') {
      const serviceExists = availableServices.some(s => s.id === selectedService)
      if (!serviceExists) {
        selectedService = 'all'
      }
    }
  })

  // Déploiements filtrés
  let filteredDeployments = $derived.by(() => {
    let filtered = deployments

    if (selectedPack) {
      filtered = filtered.filter(d => d.service_pack_id === selectedPack.id)
    }

    if (selectedService !== 'all') {
      filtered = filtered.filter(d => d.service_id === selectedService)
    }

    if (searchQuery) {
      const query = searchQuery.toLowerCase()
      filtered = filtered.filter(d =>
        d.name.toLowerCase().includes(query) ||
        d.service_name?.toLowerCase().includes(query)
      )
    }

    if (statusFilter !== 'all') {
      filtered = filtered.filter(d => d.status === statusFilter)
    }

    if (datacenterFilter !== 'all') {
      filtered = filtered.filter(d => d.datacenter === datacenterFilter)
    }

    return filtered
  })

  // Datacenters uniques
  let datacenters = $derived.by(() => {
    const dcs = new Set(deployments.map(d => d.datacenter).filter(Boolean))
    return Array.from(dcs)
  })
</script>

<DashboardLayout title="Déploiements" subtitle="Gérez tous vos services déployés">
  <div class="content-wrapper">
      <!-- Filtres -->
      <section class="filters-section" aria-label="Filtres de recherche">
        <form class="filters-bar" onsubmit={(e) => e.preventDefault()}>
          <ServicePackSelector
            {servicePacks}
            {selectedPack}
            onSelectPack={(pack) => selectedPack = pack}
          />

          <label class="visually-hidden" for="service-filter">Filtrer par service</label>
          <select id="service-filter" bind:value={selectedService} class="filter-select">
            <option value="all">Tous les services</option>
            {#each availableServices as service}
              <option value={service.id}>{service.name}</option>
            {/each}
          </select>

          <div class="search-box">
            <Search size={18} aria-hidden="true" />
            <label class="visually-hidden" for="search-input">Rechercher un déploiement</label>
            <input
              id="search-input"
              type="search"
              placeholder="Rechercher un déploiement..."
              bind:value={searchQuery}
            />
          </div>

          <label class="visually-hidden" for="status-filter">Filtrer par statut</label>
          <select id="status-filter" bind:value={statusFilter} class="filter-select">
            <option value="all">Tous les statuts</option>
            <option value="running">En cours</option>
            <option value="stopped">Arrêté</option>
            <option value="pending">En attente</option>
            <option value="error">Erreur</option>
          </select>

          <label class="visually-hidden" for="datacenter-filter">Filtrer par datacenter</label>
          <select id="datacenter-filter" bind:value={datacenterFilter} class="filter-select">
            <option value="all">Tous les datacenters</option>
            {#each datacenters as dc}
              <option value={dc}>{dc}</option>
            {/each}
          </select>
        </form>
      </section>

      <!-- Stats -->
      <section class="stats-section" aria-label="Statistiques">
        <article class="stat-card">
          <data class="stat-value" value={deployments.length}>{deployments.length}</data>
          <span class="stat-label">Total</span>
        </article>
        <article class="stat-card">
          <data class="stat-value" value={deployments.filter(d => d.status === 'running').length}>
            {deployments.filter(d => d.status === 'running').length}
          </data>
          <span class="stat-label">En cours</span>
        </article>
        <article class="stat-card">
          <data class="stat-value" value={datacenters.length}>{datacenters.length}</data>
          <span class="stat-label">Datacenters</span>
        </article>
      </section>

      <!-- Layout 2 colonnes -->
      <div class="content-grid">
        <!-- Colonne gauche: Déploiements -->
        <section class="deployments-section" aria-label="Liste des déploiements">
          {#if filteredDeployments.length === 0}
            <p class="empty-deployments">Aucun déploiement trouvé</p>
          {:else}
            <ul class="deployments-grid">
              {#each filteredDeployments as deployment (deployment.id)}
                <li>
                  <DeploymentCard {deployment} />
                </li>
              {/each}
            </ul>
          {/if}
        </section>

        <!-- Colonne droite: Carte -->
        <aside class="map-section" aria-label="Carte des datacenters">
          <DatacenterMap deployments={filteredDeployments} />
        </aside>
      </div>
  </div>
</DashboardLayout>

<style>
  .content-wrapper {
    padding: 2rem 2.5rem;
  }

  .loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    gap: 1rem;
  }

  :global(.spinner) {
    animation: spin 1s linear infinite;
    color: var(--color-primary);
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .loading p {
    color: var(--text-secondary);
    font-size: 0.875rem;
  }

  .error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
  }

  .error-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
  }

  .error-state h3 {
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
  }

  .error-state p {
    color: var(--text-secondary);
    margin: 0 0 1.5rem 0;
    font-size: 0.875rem;
  }

  .retry-button {
    background: var(--color-primary);
    color: white;
    border: none;
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
  }

  .retry-button:hover {
    background: var(--color-primary-dark);
  }

  /* Utilitaires */
  .visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
  }

  /* Filtres */
  .filters-section {
    margin-bottom: 1.5rem;
  }

  .filters-bar {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .search-box {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    flex: 1;
    min-width: 250px;
    transition: border-color 0.2s;
  }

  .search-box:focus-within {
    border-color: var(--color-primary);
  }

  .search-box input {
    flex: 1;
    border: none;
    outline: none;
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem;
    color: var(--text-primary);
    background: transparent;
  }

  .search-box input::placeholder {
    color: var(--text-tertiary);
  }

  .search-box input:focus {
    outline: none;
  }

  .filter-select {
    padding: 0.75rem 1rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.2s;
  }

  .filter-select:hover {
    border-color: var(--color-primary);
  }

  .filter-select:focus,
  .filter-select:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
    border-color: var(--color-primary);
  }

  /* Stats */
  .stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .stat-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-primary);
  }

  .stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
  }

  /* Grid layout */
  .content-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
  }

  .deployments-section {
    min-width: 0;
  }

  .deployments-grid {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
  }

  .empty-deployments {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-tertiary);
  }

  .map-section {
    position: sticky;
    top: 2rem;
    height: fit-content;
  }

  @media (max-width: 1200px) {
    .content-grid {
      grid-template-columns: 1fr;
    }

    .map-section {
      position: static;
    }
  }

  @media (max-width: 768px) {
    .content-wrapper {
      padding: 1.5rem;
    }

    .deployments-grid {
      grid-template-columns: 1fr;
    }

    .stats-section {
      grid-template-columns: 1fr;
    }
  }
</style>
