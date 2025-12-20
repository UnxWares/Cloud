<script>
  import { Server, MapPin, Activity, MoreVertical } from 'lucide-svelte'

  let { deployment } = $props()

  const statusColors = {
    running: { bg: '#ecfdf5', text: '#059669', label: 'En cours' },
    stopped: { bg: '#eff6ff', text: '#dc2626', label: 'Arrêté' },
    pending: { bg: '#fef3c7', text: '#d97706', label: 'En attente' },
    error: { bg: '#fee2e2', text: '#991b1b', label: 'Erreur' }
  }

  const status = statusColors[deployment.status] || statusColors.pending
</script>

<article class="deployment-card">
  <header class="card-header">
    <div class="deployment-icon" aria-hidden="true">
      <Server size={20} />
    </div>
    <div class="deployment-info">
      <h3>{deployment.name}</h3>
      <p class="meta">
        {#if deployment.service_name}
          <span class="service-badge">{deployment.service_name}</span>
        {/if}
        {#if deployment.datacenter}
          <span class="location">
            <MapPin size={12} aria-hidden="true" />
            {deployment.datacenter}
          </span>
        {/if}
      </p>
    </div>
    <button class="menu-button" aria-label="Options pour {deployment.name}">
      <MoreVertical size={18} aria-hidden="true" />
    </button>
  </header>

  <section class="card-body">
    <div class="status-row">
      <span class="status-badge" style="background: {status.bg}; color: {status.text}">
        <Activity size={12} aria-hidden="true" />
        {status.label}
      </span>
      {#if deployment.ip}
        <data class="ip-address" value={deployment.ip}>{deployment.ip}</data>
      {/if}
    </div>

    {#if deployment.specs}
      <dl class="specs">
        {#if deployment.specs.cpu}
          <div class="spec-item">
            <dt class="visually-hidden">CPU</dt>
            <dd>{deployment.specs.cpu} vCPU</dd>
          </div>
        {/if}
        {#if deployment.specs.ram}
          <div class="spec-item">
            <dt class="visually-hidden">RAM</dt>
            <dd>{deployment.specs.ram} GB RAM</dd>
          </div>
        {/if}
        {#if deployment.specs.storage}
          <div class="spec-item">
            <dt class="visually-hidden">Stockage</dt>
            <dd>{deployment.specs.storage} GB</dd>
          </div>
        {/if}
      </dl>
    {/if}
  </section>
</article>

<style>
  .deployment-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s;
  }

  .deployment-card:hover {
    border-color: var(--color-primary);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .deployment-card:focus-within {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(5, 12, 156, 0.1);
  }

  .card-header {
    display: flex;
    align-items: start;
    gap: 0.75rem;
    padding: 1rem;
    border-bottom: 1px solid var(--border-color-light);
  }

  .deployment-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--bg-secondary);
    border-radius: 6px;
    color: var(--text-secondary);
    flex-shrink: 0;
  }

  .deployment-info {
    flex: 1;
    min-width: 0;
  }

  h3 {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

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

  .meta {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .service-badge {
    display: inline-flex;
    padding: 0.125rem 0.5rem;
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 500;
  }

  .location {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: var(--text-tertiary);
  }

  .menu-button {
    background: transparent;
    border: none;
    color: var(--text-tertiary);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s;
  }

  .menu-button:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
  }

  .menu-button:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
    background: var(--bg-tertiary);
    color: var(--text-primary);
  }

  .card-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8125rem;
    font-weight: 500;
  }

  .ip-address {
    font-family: 'Courier New', monospace;
    font-size: 0.8125rem;
    color: var(--text-secondary);
  }

  .specs {
    margin: 0;
    padding: 0;
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  .spec-item {
    display: contents;
  }

  .spec-item dd {
    margin: 0;
    font-size: 0.8125rem;
    color: var(--text-secondary);
  }
</style>
