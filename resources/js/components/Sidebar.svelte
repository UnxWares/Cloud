<script>
  import { LayoutDashboard, Settings, ChevronDown, Package, Moon, Sun, PanelLeftClose, PanelLeft } from 'lucide-svelte'
  import * as LucideIcons from 'lucide-svelte'
  import { onMount } from 'svelte'
  import { api } from '../lib/api.js'
  import { theme, toggleTheme } from '../lib/theme.js'
  import { sidebarCollapsed } from '../lib/sidebar.js'

  let servicePacks = $state([])
  let openPacks = $state({})
  let currentPath = $state(window.location.pathname)

  // Fonction pour obtenir l'icône Lucide depuis son nom
  function getIcon(iconName) {
    if (!iconName) return Package

    // Convertir le nom en PascalCase si nécessaire (ex: "hard-drive" -> "HardDrive")
    const pascalCase = iconName
      .split('-')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join('')

    return LucideIcons[pascalCase] || Package
  }

  async function loadServicePacks() {
    try {
      const data = await api.getServicePacks().catch(() => [])
      servicePacks = data || []

      // Données de démo si vide
      if (servicePacks.length === 0) {
        servicePacks = [
          { id: 'storage', slug: 'storage', name: 'Storage', icon: 'HardDrive', services: [] },
          { id: 'compute', slug: 'compute', name: 'Compute', icon: 'Server', services: [] },
          { id: 'network', slug: 'network', name: 'Network', icon: 'Globe', services: [] }
        ]
      }

      // Charger les services de chaque pack
      for (const pack of servicePacks) {
        try {
          const packSlug = pack.slug || pack.id
          if (packSlug) {
            const services = await api.getPackServices(packSlug).catch(() => [])
            pack.services = services || []
          } else {
            pack.services = []
          }
        } catch (err) {
          console.error(`Erreur chargement services pour ${pack.name}:`, err)
          pack.services = []
        }
      }

      // Ouvrir tous les packs par défaut
      servicePacks.forEach(pack => {
        openPacks[pack.id || pack.slug] = true
      })
    } catch (err) {
      console.error('Erreur chargement service packs:', err)
    }
  }

  function togglePack(packId) {
    if ($sidebarCollapsed) return // Ne pas ouvrir les packs en mode collapsed
    openPacks[packId] = !openPacks[packId]
  }

  function toggleCollapse() {
    sidebarCollapsed.update(val => !val)
    // Fermer tous les packs quand on collapse
    if ($sidebarCollapsed) {
      openPacks = {}
    } else {
      // Rouvrir tous les packs quand on expand
      servicePacks.forEach(pack => {
        openPacks[pack.id || pack.slug] = true
      })
    }
  }

  onMount(() => {
    loadServicePacks()
  })
</script>

<aside class="sidebar" class:collapsed={$sidebarCollapsed} role="complementary" aria-label="Navigation principale">
  <div class="logo">
    <a href="/" aria-label="Retour à l'accueil">
      <img src="/resources/img/unxwares-icon.svg" alt="Logo UnxWares" class="logo-icon" />
      {#if !$sidebarCollapsed}
        <span class="logo-text">Dashboard</span>
      {/if}
    </a>
  </div>

  <nav class="nav" aria-label="Menu principal">
    <!-- Dashboard -->
    <a
      href="/dashboard"
      class="nav-item"
      class:active={currentPath === '/dashboard'}
      aria-current={currentPath === '/dashboard' ? 'page' : undefined}
    >
      <LayoutDashboard size={20} aria-hidden="true" />
      <span class="label">Dashboard</span>
    </a>

    <!-- Service Packs (dropdown) -->
    <div class="nav-section">
      {#if !$sidebarCollapsed}
        <div class="section-title" role="heading" aria-level="2">Services</div>
      {/if}
      {#each servicePacks as pack}
        <div class="pack-group">
          <button
            class="nav-item pack-toggle"
            onclick={() => togglePack(pack.id)}
            aria-expanded={!$sidebarCollapsed && openPacks[pack.id]}
            aria-label="Afficher/masquer les services {pack.name}"
            title={$sidebarCollapsed ? pack.name : ''}
          >
            <svelte:component this={getIcon(pack.icon)} size={20} aria-hidden="true" />
            {#if !$sidebarCollapsed}
              <span class="label">{pack.name}</span>
              <div class="chevron" class:open={openPacks[pack.id]} aria-hidden="true">
                <ChevronDown size={16} />
              </div>
            {/if}
          </button>

          {#if !$sidebarCollapsed && openPacks[pack.id]}
            <div class="pack-services">
              {#if pack.services && pack.services.length > 0}
                {#each pack.services as service}
                  <a
                    href="/dashboard/{pack.slug || pack.id}/{service.slug || service.id}"
                    class="service-item"
                  >
                    {service.name || service.slug}
                  </a>
                {/each}
              {:else}
                <div class="service-item empty">
                  Aucun service disponible
                </div>
              {/if}
            </div>
          {/if}
        </div>
      {/each}
    </div>

    <!-- Paramètres (en bas) -->
    <div class="nav-bottom">
      <button
        class="nav-item"
        onclick={toggleTheme}
        aria-label={$theme === 'dark' ? 'Activer le mode clair' : 'Activer le mode sombre'}
      >
        {#if $theme === 'dark'}
          <Sun size={20} aria-hidden="true" />
          <span class="label">Mode clair</span>
        {:else}
          <Moon size={20} aria-hidden="true" />
          <span class="label">Mode sombre</span>
        {/if}
      </button>
      <a
        href="/dashboard/settings"
        class="nav-item"
        class:active={currentPath === '/dashboard/settings'}
        aria-current={currentPath === '/dashboard/settings' ? 'page' : undefined}
      >
        <Settings size={20} aria-hidden="true" />
        <span class="label">Paramètres</span>
      </a>
      <button
        class="nav-item"
        onclick={toggleCollapse}
        aria-label={$sidebarCollapsed ? 'Déplier la barre latérale' : 'Replier la barre latérale'}
      >
        {#if $sidebarCollapsed}
          <PanelLeft size={20} aria-hidden="true" />
          <span class="label">Déplier</span>
        {:else}
          <PanelLeftClose size={20} aria-hidden="true" />
          <span class="label">Replier</span>
        {/if}
      </button>
    </div>
  </nav>
</aside>

<style>
  .sidebar {
    width: 240px;
    background: var(--bg-primary);
    border-right: 1px solid var(--border-color);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
  }

  .sidebar.collapsed {
    width: 60px;
  }

  .logo {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .logo a {
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
  }

  .logo-icon {
    height: 40px;
    width: auto;
    transition: transform 0.2s;
  }

  .sidebar.collapsed .logo-icon {
    height: 32px;
  }

  .logo-text {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--color-primary);
    transition: opacity 0.2s;
  }

  .logo a:hover .logo-icon {
    transform: scale(1.05);
  }

  .logo a:hover .logo-text {
    opacity: 0.8;
  }

  .nav {
    flex: 1;
    padding: 1rem 0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
  }

  .nav-section {
    margin-top: 1rem;
  }

  .section-title {
    padding: 0.5rem 1.5rem;
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-tertiary);
  }

  .pack-group {
    margin-bottom: 0.25rem;
  }

  .nav-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    background: transparent;
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    border: none;
    text-align: left;
  }

  .nav-item:hover {
    background: var(--bg-secondary);
    color: var(--text-primary);
  }

  .nav-item.active {
    background: var(--color-primary-bg);
    color: var(--color-primary);
    font-weight: 500;
    border-left: 3px solid var(--color-primary);
  }

  .pack-toggle {
    justify-content: space-between;
  }

  .sidebar.collapsed .pack-toggle {
    justify-content: center;
  }

  .label {
    flex: 1;
  }

  .sidebar.collapsed .label,
  .sidebar.collapsed .logo-text,
  .sidebar.collapsed .chevron,
  .sidebar.collapsed .section-title {
    display: none;
  }

  .sidebar.collapsed .nav-item {
    justify-content: center;
    padding: 0.75rem;
  }

  .chevron {
    display: flex;
    color: var(--text-tertiary);
    transition: transform 0.2s;
  }

  .chevron.open {
    transform: rotate(180deg);
  }

  .pack-services {
    background: var(--bg-secondary);
    padding: 0.25rem 0;
  }

  .service-item {
    display: block;
    padding: 0.625rem 1.5rem 0.625rem 3.5rem;
    color: var(--text-secondary);
    font-size: 0.8125rem;
    text-decoration: none;
    transition: all 0.2s;
  }

  .service-item:hover {
    color: var(--color-primary);
    background: var(--bg-primary);
  }

  .service-item.empty {
    color: var(--text-tertiary);
    font-style: italic;
    cursor: default;
  }

  .service-item.empty:hover {
    color: var(--text-tertiary);
    background: transparent;
  }

  .nav-bottom {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
  }

  @media (max-width: 768px) {
    .sidebar {
      width: 60px;
    }

    .logo-icon {
      height: 32px;
    }

    .logo-text,
    .label,
    .chevron,
    .section-title {
      display: none;
    }

    .nav-item {
      justify-content: center;
      padding: 0.75rem;
    }

    .nav-item.active {
      border-left: none;
      border-right: 3px solid var(--color-primary);
    }

    .pack-services {
      display: none;
    }
  }
</style>
