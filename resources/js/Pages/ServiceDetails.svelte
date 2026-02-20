<script>
  import { Server, ArrowLeft, Plus } from 'lucide-svelte'
  import { router } from '@inertiajs/svelte'
  import DashboardLayout from '../layouts/DashboardLayout.svelte'
  import DeploymentCard from '../components/DeploymentCard.svelte'

  // Props depuis Inertia
  let { packSlug, serviceSlug, offers = [], deployments = [] } = $props()

  // Formater le nom du service
  let serviceName = $derived(serviceSlug.charAt(0).toUpperCase() + serviceSlug.slice(1))

  // État pour le modal de création
  let showCreateModal = $state(false)
  let selectedOffer = $state(null)
  let deploymentName = $state('')

  function openCreateModal() {
    showCreateModal = true
  }

  function closeCreateModal() {
    showCreateModal = false
    selectedOffer = null
    deploymentName = ''
  }

  function createDeployment() {
    if (!deploymentName || !selectedOffer) return

    router.post(`/dashboard/${packSlug}/${serviceSlug}/deployments`, {
      name: deploymentName,
      offer_id: selectedOffer,
    }, {
      onSuccess: () => {
        closeCreateModal()
      }
    })
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
        <button class="new-deployment-button" onclick={openCreateModal}>
          <Plus size={20} />
          <span>Nouveau déploiement</span>
        </button>
      </div>

      {#if deployments.length === 0}
        <div class="empty-state">
          <Server size={48} />
          <p>Aucun déploiement actif</p>
          <button class="create-first-button" onclick={openCreateModal}>
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

<!-- Modal de création de déploiement -->
{#if showCreateModal}
  <div class="modal-overlay" onclick={closeCreateModal}>
    <div class="modal-content" onclick={(e) => e.stopPropagation()}>
      <h2>Créer un nouveau déploiement</h2>

      <form onsubmit={(e) => { e.preventDefault(); createDeployment(); }}>
        <div class="form-group">
          <label for="deployment-name">Nom du déploiement</label>
          <input
            id="deployment-name"
            type="text"
            bind:value={deploymentName}
            placeholder="Ex: Mon serveur web"
            required
          />
        </div>

        <div class="form-group">
          <label for="offer-select">Offre</label>
          <select id="offer-select" bind:value={selectedOffer} required>
            <option value="">Sélectionner une offre</option>
            {#each offers as offer}
              <option value={offer.id}>{offer.name} - {offer.price}</option>
            {/each}
          </select>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick={closeCreateModal}>Annuler</button>
          <button type="submit" class="btn-create" disabled={!deploymentName || !selectedOffer}>
            <Plus size={18} />
            <span>Créer</span>
          </button>
        </div>
      </form>
    </div>
  </div>
{/if}

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

    .deployments-grid {
      grid-template-columns: 1fr;
    }
  }

  /* Modal */
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
  }

  .modal-content {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 2rem;
    max-width: 500px;
    width: 100%;
    box-shadow: var(--shadow-lg);
  }

  .modal-content h2 {
    margin: 0 0 1.5rem 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
  }

  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
  }

  .form-group input,
  .form-group select {
    width: 100%;
    padding: 0.75rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem;
    color: var(--text-primary);
    transition: border-color 0.2s;
  }

  .form-group input:focus,
  .form-group select:focus {
    outline: none;
    border-color: var(--color-primary);
  }

  .modal-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 2rem;
  }

  .btn-cancel,
  .btn-create {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-cancel {
    background: var(--bg-tertiary);
    color: var(--text-primary);
  }

  .btn-cancel:hover {
    background: var(--border-color);
  }

  .btn-create {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--color-primary);
    color: white;
  }

  .btn-create:hover:not(:disabled) {
    background: var(--color-primary-dark);
  }

  .btn-create:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
</style>
