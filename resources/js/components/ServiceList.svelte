<script>
  import { Server, ChevronRight } from 'lucide-svelte'

  let { services = [], onSelectService } = $props()
</script>

<div class="services-container">
  <h2>Services disponibles</h2>

  <div class="services-list">
    {#each services as service}
      <button class="service-item" onclick={() => onSelectService(service)}>
        <div class="service-icon">
          <Server size={24} />
        </div>
        <div class="service-details">
          <h3>{service.name}</h3>
          {#if service.description}
            <p>{service.description}</p>
          {/if}
          {#if service.deployments_count !== undefined}
            <span class="deployments-badge">
              {service.deployments_count} déploiement{service.deployments_count > 1 ? 's' : ''}
            </span>
          {/if}
        </div>
        <ChevronRight size={20} class="chevron" />
      </button>
    {/each}
  </div>
</div>

<style>
  .services-container {
    margin-top: 2rem;
  }

  h2 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 1rem 0;
  }

  .services-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .service-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
  }

  .service-item:hover {
    border-color: var(--color-primary);
    box-shadow: 0 2px 8px rgba(217, 48, 37, 0.1);
  }

  .service-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: var(--bg-secondary);
    border-radius: 8px;
    color: var(--text-secondary);
    flex-shrink: 0;
  }

  .service-details {
    flex: 1;
    min-width: 0;
  }

  h3 {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
  }

  p {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    margin: 0 0 0.5rem 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .deployments-badge {
    display: inline-flex;
    padding: 0.125rem 0.5rem;
    background: #ecfdf5;
    color: #059669;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 500;
  }

  :global(.chevron) {
    color: var(--text-tertiary);
    flex-shrink: 0;
  }
</style>
