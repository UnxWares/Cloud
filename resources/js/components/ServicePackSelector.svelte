<script>
  import { ChevronDown, Package, ChevronRight } from 'lucide-svelte'

  let { servicePacks = [], selectedPack = null, onSelectPack } = $props()
  let isOpen = $state(false)
  let expandedPacks = $state(new Set())

  function togglePackExpand(packId, event) {
    event.stopPropagation()
    const newExpanded = new Set(expandedPacks)
    if (newExpanded.has(packId)) {
      newExpanded.delete(packId)
    } else {
      newExpanded.add(packId)
    }
    expandedPacks = newExpanded
  }
</script>

<div class="pack-selector">
  <button class="selector-button" onclick={() => isOpen = !isOpen}>
    <div class="selector-content">
      <Package size={20} />
      <div class="selector-text">
        <span class="label">Service Pack</span>
        <span class="value">{selectedPack?.name || 'Sélectionner un pack'}</span>
      </div>
    </div>
    <div class="chevron-icon" class:rotated={isOpen}>
      <ChevronDown size={20} />
    </div>
  </button>

  {#if isOpen}
    <div class="dropdown">
      <!-- Option "Tous les packs" -->
      <button
        class="dropdown-item"
        class:active={selectedPack === null}
        onclick={() => {
          onSelectPack(null)
          isOpen = false
        }}
      >
        <div class="pack-info">
          <span class="pack-name">Tous les packs</span>
          <span class="pack-desc">Afficher tous les déploiements</span>
        </div>
      </button>

      {#each servicePacks as pack}
        <div class="pack-group">
          <button
            class="dropdown-item pack-item"
            class:active={selectedPack?.id === pack.id}
            onclick={() => {
              onSelectPack(pack)
              isOpen = false
            }}
          >
            <div class="pack-info">
              <span class="pack-name">{pack.name}</span>
              {#if pack.description}
                <span class="pack-desc">{pack.description}</span>
              {/if}
            </div>
            <div class="pack-actions">
              {#if pack.services && pack.services.length > 0}
                <button
                  class="expand-button"
                  class:expanded={expandedPacks.has(pack.id)}
                  onclick={(e) => togglePackExpand(pack.id, e)}
                >
                  <ChevronRight size={16} />
                </button>
              {/if}
              {#if pack.count !== undefined}
                <span class="pack-count">{pack.count}</span>
              {/if}
            </div>
          </button>

          {#if pack.services && pack.services.length > 0 && expandedPacks.has(pack.id)}
            <div class="services-list">
              {#each pack.services as service}
                <div class="service-item">
                  <span class="service-name">{service.name || service.slug}</span>
                  {#if service.description}
                    <span class="service-desc">{service.description}</span>
                  {/if}
                </div>
              {/each}
            </div>
          {/if}
        </div>
      {/each}
    </div>
  {/if}
</div>

<style>
  .pack-selector {
    position: relative;
    width: 100%;
    max-width: 400px;
  }

  .selector-button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
  }

  .selector-button:hover {
    border-color: var(--color-primary);
  }

  .selector-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--text-primary);
  }

  .selector-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.125rem;
  }

  .label {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    font-weight: 500;
  }

  .value {
    font-size: 0.875rem;
    color: var(--text-primary);
    font-weight: 500;
  }

  .chevron-icon {
    display: flex;
    color: var(--text-primary);
    transition: transform 0.2s;
  }

  .chevron-icon.rotated {
    transform: rotate(180deg);
  }

  .dropdown {
    position: absolute;
    top: calc(100% + 0.5rem);
    left: 0;
    right: 0;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    z-index: 50;
    max-height: 400px;
    overflow-y: auto;
  }

  .dropdown-item {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem 1rem;
    background: transparent;
    border: none;
    border-bottom: 1px solid var(--border-color-light);
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: background 0.2s;
    text-align: left;
  }

  .dropdown-item:last-child {
    border-bottom: none;
  }

  .dropdown-item:hover {
    background: var(--bg-secondary);
  }

  .dropdown-item.active {
    background: var(--color-primary-bg);
  }

  .pack-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
  }

  .pack-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
  }

  .pack-desc {
    font-size: 0.75rem;
    color: var(--text-secondary);
  }

  .pack-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.5rem;
    height: 1.5rem;
    padding: 0 0.5rem;
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .pack-group {
    width: 100%;
  }

  .pack-item {
    position: relative;
  }

  .pack-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .expand-button {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem;
    background: transparent;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 4px;
  }

  .expand-button:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
  }

  .expand-button.expanded {
    transform: rotate(90deg);
  }

  .services-list {
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-color-light);
    padding: 0.5rem 0;
  }

  .service-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 0.625rem 1rem 0.625rem 3rem;
    transition: background 0.2s;
  }

  .service-item:hover {
    background: var(--bg-tertiary);
  }

  .service-name {
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--text-primary);
  }

  .service-desc {
    font-size: 0.75rem;
    color: var(--text-tertiary);
  }
</style>
