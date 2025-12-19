<script>
  import { ChevronRight } from 'lucide-svelte'

  let { category } = $props()
</script>

<div class="category-card">
  <div class="card-content">
    <div class="card-header">
      <h3>{category.name}</h3>
      {#if category.count !== undefined}
        <span class="badge">{category.count} services</span>
      {/if}
    </div>

    {#if category.description}
      <p class="description">{category.description}</p>
    {/if}
  </div>

  <button class="view-button">
    <span>Voir les services</span>
    <ChevronRight size={16} />
  </button>
</div>

<style>
  .category-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 1.5rem;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .category-card:hover {
    border-color: var(--color-primary);
    box-shadow: 0 4px 12px rgba(217, 48, 37, 0.08);
  }

  .card-content {
    flex: 1;
  }

  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.75rem;
    gap: 1rem;
  }

  h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
  }

  .badge {
    display: inline-flex;
    padding: 0.25rem 0.75rem;
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
  }

  .description {
    color: var(--text-secondary);
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
  }

  .view-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 0.875rem;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
  }

  .view-button:hover {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
  }
</style>
