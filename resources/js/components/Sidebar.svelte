<script>
  import { LayoutDashboard, Settings, ChevronDown, Package, Moon, Sun, PanelLeftClose, PanelLeft } from 'lucide-svelte'
  import * as LucideIcons from 'lucide-svelte'
  import { page } from '@inertiajs/svelte'
  import { theme, toggleTheme } from '../lib/theme.js'
  import { sidebarCollapsed } from '../lib/sidebar.js'

  // Récupérer les servicePacks depuis les props Inertia partagées
  let servicePacks = $derived($page.props.servicePacks || [])
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

  // Ouvrir tous les packs par défaut quand les servicePacks changent
  $effect(() => {
    if (servicePacks.length > 0 && Object.keys(openPacks).length === 0) {
      servicePacks.forEach(pack => {
        openPacks[pack.id || pack.slug] = true
      })
    }
  })

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
</script>

<aside class="sidebar" class:collapsed={$sidebarCollapsed}>
  <header class="logo">
    <a href="/" aria-label="Retour à l'accueil">
      <img src="/resources/img/unxwares-icon.svg" alt="Logo UnxWares" class="logo-icon" />
      {#if !$sidebarCollapsed}
        <span class="logo-text">Dashboard</span>
      {/if}
    </a>
  </header>

  <nav class="nav" aria-label="Navigation principale">
    <ul class="nav-list">
      <!-- Dashboard -->
      <li>
        <a
          href="/dashboard"
          class="nav-item"
          class:active={currentPath === '/dashboard'}
          aria-current={currentPath === '/dashboard' ? 'page' : undefined}
        >
          <LayoutDashboard size={20} aria-hidden="true" />
          <span class="label">Dashboard</span>
        </a>
      </li>
    </ul>

    <!-- Service Packs -->
    {#if !$sidebarCollapsed}
      <h2 class="section-title" id="services-heading">Services</h2>
    {/if}

    <ul class="nav-list" aria-labelledby={!$sidebarCollapsed ? 'services-heading' : undefined}>
      {#each servicePacks as pack (pack.id)}
        <li class="pack-group">
          <button
            class="nav-item pack-toggle"
            onclick={() => togglePack(pack.id)}
            aria-expanded={!$sidebarCollapsed ? openPacks[pack.id] : undefined}
            aria-controls={`pack-services-${pack.id}`}
            aria-label={$sidebarCollapsed ? pack.name : undefined}
            title={$sidebarCollapsed ? `Ouvrir ${pack.name}` : undefined}
            type="button"
          >
            <svelte:component this={getIcon(pack.icon)} size={20} aria-hidden="true" />
            {#if !$sidebarCollapsed}
              <span class="label">{pack.name}</span>
              <span class="chevron" class:open={openPacks[pack.id]} aria-hidden="true">
                <ChevronDown size={16} />
              </span>
            {/if}
          </button>

          {#if !$sidebarCollapsed && openPacks[pack.id]}
            <ul class="pack-services" id={`pack-services-${pack.id}`} role="group" aria-label="Services {pack.name}">
              {#if pack.services && pack.services.length > 0}
                {#each pack.services as service (service.id || service.slug)}
                  <li>
                    <a
                      href="/dashboard/{pack.slug || pack.id}/{service.slug || service.id}"
                      class="service-item"
                    >
                      {service.name || service.slug}
                    </a>
                  </li>
                {/each}
              {:else}
                <li>
                  <span class="service-item empty" role="status">
                    Aucun service disponible
                  </span>
                </li>
              {/if}
            </ul>
          {/if}
        </li>
      {/each}
    </ul>

    <!-- Actions -->
    <ul class="nav-bottom" aria-label="Actions et paramètres">
      <li>
        <button
          class="nav-item"
          onclick={toggleTheme}
          aria-label={$theme === 'dark' ? 'Activer le mode clair' : 'Activer le mode sombre'}
          aria-pressed={$theme === 'dark'}
          type="button"
        >
          {#if $theme === 'dark'}
            <Sun size={20} aria-hidden="true" />
            <span class="label">Mode clair</span>
          {:else}
            <Moon size={20} aria-hidden="true" />
            <span class="label">Mode sombre</span>
          {/if}
        </button>
      </li>
      <li>
        <a
          href="/dashboard/settings"
          class="nav-item"
          class:active={currentPath === '/dashboard/settings'}
          aria-current={currentPath === '/dashboard/settings' ? 'page' : undefined}
        >
          <Settings size={20} aria-hidden="true" />
          <span class="label">Paramètres</span>
        </a>
      </li>
      <li>
        <button
          class="nav-item"
          onclick={toggleCollapse}
          aria-label={$sidebarCollapsed ? 'Déplier la barre latérale' : 'Replier la barre latérale'}
          aria-expanded={!$sidebarCollapsed}
          aria-controls="main-content"
          type="button"
        >
          {#if $sidebarCollapsed}
            <PanelLeft size={20} aria-hidden="true" />
            <span class="label">Déplier</span>
          {:else}
            <PanelLeftClose size={20} aria-hidden="true" />
            <span class="label">Replier</span>
          {/if}
        </button>
      </li>
    </ul>
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

  .nav-list {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .section-title {
    padding: 0.5rem 1.5rem;
    margin: 1rem 0 0.5rem;
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

  .nav-item:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: -2px;
    background: var(--bg-secondary);
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
    list-style: none;
    margin: 0;
    padding: 0.25rem 0;
    background: var(--bg-secondary);
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
    list-style: none;
    margin: auto 0 0;
    padding: 1rem 0 0;
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
