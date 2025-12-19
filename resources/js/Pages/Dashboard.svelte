<script>
  import { Search } from 'lucide-svelte'
  import Sidebar from '../components/Sidebar.svelte'
  import DeploymentCard from '../components/DeploymentCard.svelte'
  import DatacenterMap from '../components/DatacenterMap.svelte'
  import ServicePackSelector from '../components/ServicePackSelector.svelte'
  import { sidebarCollapsed, SIDEBAR_WIDTH_EXPANDED, SIDEBAR_WIDTH_COLLAPSED } from '../lib/sidebar.js'

  // Props depuis Inertia (passées depuis Laravel)
  let { servicePacks = [], deployments = [] } = $props()

  // Largeur de la sidebar selon l'état
  let sidebarWidth = $derived($sidebarCollapsed ? SIDEBAR_WIDTH_COLLAPSED : SIDEBAR_WIDTH_EXPANDED)

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

<div class="dashboard-layout">
  <Sidebar />

  <main class="main-content" style="--sidebar-width: {sidebarWidth}px">
    <header class="page-header">
      <div class="header-content">
        <h1>Déploiements</h1>
        <p class="subtitle">Gérez tous vos services déployés</p>
      </div>
    </header>

    <div class="content-wrapper">
      <!-- Filtres -->
      <div class="filters-bar">
          <ServicePackSelector
            {servicePacks}
            {selectedPack}
            onSelectPack={(pack) => selectedPack = pack}
          />

          <select bind:value={selectedService} class="filter-select" aria-label="Filtrer par service">
            <option value="all">Tous les services</option>
            {#each availableServices as service}
              <option value={service.id}>{service.name}</option>
            {/each}
          </select>

          <div class="search-box">
            <Search size={18} />
            <input
              type="text"
              placeholder="Rechercher un déploiement..."
              bind:value={searchQuery}
              aria-label="Rechercher un déploiement"
            />
          </div>

          <select bind:value={statusFilter} class="filter-select" aria-label="Filtrer par statut">
            <option value="all">Tous les statuts</option>
            <option value="running">En cours</option>
            <option value="stopped">Arrêté</option>
            <option value="pending">En attente</option>
            <option value="error">Erreur</option>
          </select>

          <select bind:value={datacenterFilter} class="filter-select" aria-label="Filtrer par datacenter">
            <option value="all">Tous les datacenters</option>
            {#each datacenters as dc}
              <option value={dc}>{dc}</option>
            {/each}
          </select>
        </div>

        <!-- Stats -->
        <div class="stats-row">
          <div class="stat-card">
            <span class="stat-value">{deployments.length}</span>
            <span class="stat-label">Total</span>
          </div>
          <div class="stat-card">
            <span class="stat-value">{deployments.filter(d => d.status === 'running').length}</span>
            <span class="stat-label">En cours</span>
          </div>
          <div class="stat-card">
            <span class="stat-value">{datacenters.length}</span>
            <span class="stat-label">Datacenters</span>
          </div>
        </div>

        <!-- Layout 2 colonnes -->
        <div class="content-grid">
          <!-- Colonne gauche: Déploiements -->
          <div class="deployments-section">
            {#if filteredDeployments.length === 0}
              <div class="empty-deployments">
                <p>Aucun déploiement trouvé</p>
              </div>
            {:else}
              <div class="deployments-grid">
                {#each filteredDeployments as deployment (deployment.id)}
                  <DeploymentCard {deployment} />
                {/each}
              </div>
            {/if}
          </div>

          <!-- Colonne droite: Carte -->
          <div class="map-section">
            <DatacenterMap deployments={filteredDeployments} />
          </div>
        </div>
    </div>
  </main>
</div>

<style>
  .dashboard-layout {
    display: flex;
    min-height: 100vh;
    background: var(--bg-secondary);
  }

  .main-content {
    flex: 1;
    margin-left: var(--sidebar-width, 240px);
    min-height: 100vh;
    transition: margin-left 0.3s ease;
  }

  .page-header {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    padding: 2rem 2.5rem;
  }

  .header-content h1 {
    margin: 0 0 0.5rem 0;
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--text-primary);
  }

  .subtitle {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
  }

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

  /* Filtres */
  .filters-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
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
  }

  .search-box input::placeholder {
    color: var(--text-tertiary);
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

  .filter-select:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(5, 12, 156, 0.1);
  }

  /* Stats */
  .stats-row {
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
    .main-content {
      margin-left: 60px;
    }

    .page-header {
      padding: 1.5rem;
    }

    .header-content h1 {
      font-size: 1.5rem;
    }

    .content-wrapper {
      padding: 1.5rem;
    }

    .deployments-grid {
      grid-template-columns: 1fr;
    }

    .stats-row {
      grid-template-columns: 1fr;
    }
  }
</style>
