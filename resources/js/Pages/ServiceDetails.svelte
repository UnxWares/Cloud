<script>
  import { Server, ArrowLeft, Plus } from 'lucide-svelte'
  import DashboardLayout from '../layouts/DashboardLayout.svelte'
  import DeploymentCard from '../components/DeploymentCard.svelte'

  // Props depuis Inertia
  let { packSlug, serviceSlug, offers = [], deployments = [] } = $props()

  // Formater le nom du service
  let serviceName = $derived(serviceSlug.charAt(0).toUpperCase() + serviceSlug.slice(1))

  function createNewDeployment() {
    // Redirection vers unxwares.cloud pour créer un nouveau déploiement
    // L'utilisateur pourra choisir son offre, enregistrer sa carte et payer
    const returnUrl = encodeURIComponent(window.location.href)
    window.location.href = `https://unxwares.cloud/products/${packSlug}/${serviceSlug}?return=${returnUrl}`
  }
</script>

<DashboardLayout title={serviceName} subtitle="Gérez vos instances {serviceName}">
  <div class="content-wrapper">
    <a href="/dashboard" class="back-link">
      <ArrowLeft size={20} />
      <span>Retour au dashboard</span>
    </a>
    <section class="section">
      <div class="section-header">
        <div class="header-left">
          <h2>Mes déploiements {serviceName}</h2>
          <p class="section-desc">Gérez vos instances actives</p>
        </div>
        <button class="new-deployment-button" onclick={createNewDeployment}>
          <Plus size={20} />
          <span>Nouveau déploiement</span>
        </button>
      </div>

      {#if deployments.length === 0}
        <div class="empty-state">
          <Server size={48} />
          <p>Aucun déploiement actif</p>
          <button class="create-first-button" onclick={createNewDeployment}>
            <Plus size={20} />
            <span>Créer votre premier déploiement</span>
          </button>
        </div>
      {:else}
        <div class="deployments-grid">
          {#each deployments as deployment (deployment.id)}
            <DeploymentCard {deployment} />
          {/each}
        </div>
      {/if}
    </section>
  </div>
</DashboardLayout>

<style>
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    transition: color 0.2s;
  }

  .back-link:hover {
    color: var(--color-primary);
  }

  .content-wrapper {
    padding: 2rem 2.5rem;
  }

  .section {
    margin-bottom: 3rem;
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
  }

  .header-left h2 {
    margin: 0 0 0.25rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
  }

  .section-desc {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
  }

  .new-deployment-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(5, 12, 156, 0.2);
  }

  .new-deployment-button:hover {
    background: var(--color-primary-dark);
    box-shadow: 0 4px 8px rgba(5, 12, 156, 0.3);
    transform: translateY(-1px);
  }

  .new-deployment-button:active {
    transform: translateY(0);
  }

  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-tertiary);
    text-align: center;
  }

  .empty-state p {
    margin: 1rem 0 1.5rem 0;
    font-size: 0.875rem;
  }

  .create-first-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.9375rem;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(5, 12, 156, 0.2);
  }

  .create-first-button:hover {
    background: var(--color-primary-dark);
    box-shadow: 0 4px 8px rgba(5, 12, 156, 0.3);
    transform: translateY(-1px);
  }

  .create-first-button:active {
    transform: translateY(0);
  }

  .deployments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1rem;
  }

  @media (max-width: 768px) {
    .content-wrapper {
      padding: 1.5rem;
    }

    .offers-grid,
    .deployments-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
