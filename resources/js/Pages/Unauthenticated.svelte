<script>
  import { Lock } from 'lucide-svelte'

  // Vérifier si on est en mode dev
  const isDev = import.meta.env.DEV
</script>

<div class="unauthenticated-container">
  <div class="unauthenticated-card">
    <div class="icon-wrapper">
      <Lock size={48} />
    </div>

    <h1>Accès non autorisé</h1>
    <p class="message">
      Vous devez être connecté pour accéder au dashboard.
    </p>

    <p class="info">
      Veuillez vous connecter en utilisant l'OIDC de <strong>customers.unxwares.com</strong>
    </p>

    {#if isDev}
      <div class="dev-section">
        <p class="dev-info">Mode développement</p>
        <a href="/dev/auth-test" class="dev-button">
          Tester l'authentification
        </a>
      </div>
    {/if}
  </div>
</div>

<style>
  .unauthenticated-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 2rem;
    background-color: var(--bg-primary);
  }

  .unauthenticated-card {
    max-width: 500px;
    width: 100%;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 3rem 2rem;
    text-align: center;
    box-shadow: var(--shadow-lg);
  }

  .icon-wrapper {
    display: inline-flex;
    padding: 1.5rem;
    background: var(--color-primary-bg);
    border-radius: 50%;
    color: var(--color-primary);
    margin-bottom: 1.5rem;
  }

  h1 {
    margin: 0 0 1rem;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
  }

  .message {
    margin: 0 0 1.5rem;
    font-size: 1rem;
    color: var(--text-secondary);
    line-height: 1.6;
  }

  .info {
    margin: 0 0 2rem;
    font-size: 0.9375rem;
    color: var(--text-secondary);
    padding: 1rem;
    background: var(--bg-tertiary);
    border-radius: 8px;
  }

  .info strong {
    color: var(--color-primary);
    font-weight: 600;
  }

  .dev-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
  }

  .dev-info {
    margin: 0 0 1rem;
    font-size: 0.875rem;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
  }

  .dev-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    background: var(--color-primary);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
  }

  .dev-button:hover {
    background: var(--color-primary-dark);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
  }

  .dev-button:active {
    transform: translateY(0);
    box-shadow: var(--shadow-sm);
  }

  @media (max-width: 640px) {
    .unauthenticated-container {
      padding: 1rem;
    }

    .unauthenticated-card {
      padding: 2rem 1.5rem;
    }

    h1 {
      font-size: 1.5rem;
    }
  }
</style>
