<script>
  import { ServerCrash, RefreshCw } from 'lucide-svelte'

  let { error = 'Service temporairement indisponible' } = $props()

  // Extraire le code d'erreur et générer un message générique
  const errorInfo = $derived(() => {
    if (!error) {
      return {
        code: 'ERR_UNKNOWN',
        message: 'Le service est temporairement indisponible'
      }
    }

    // Erreur cURL
    const curlMatch = error.match(/cURL error (\d+)/)
    if (curlMatch) {
      const code = curlMatch[1]
      let message = 'Impossible de se connecter au service'

      // Messages spécifiques selon le code cURL
      if (code === '7') message = 'Impossible de joindre le serveur (connexion refusée)'
      else if (code === '28') message = 'Délai de connexion dépassé'
      else if (code === '6') message = 'Impossible de résoudre le nom de domaine'

      return { code: `CURL_${code}`, message }
    }

    // Erreur HTTP
    const httpMatch = error.match(/(\d{3})/)
    if (httpMatch) {
      const code = httpMatch[1]
      let message = 'Le service a rencontré une erreur'

      if (code === '500') message = 'Erreur interne du serveur'
      else if (code === '503') message = 'Service temporairement indisponible'
      else if (code === '404') message = 'Service non trouvé'

      return { code: `HTTP_${code}`, message }
    }

    return {
      code: 'ERR_CONNECTION',
      message: 'Erreur de connexion au service'
    }
  })
</script>

<div class="unavailable-container">
  <div class="unavailable-card">
    <div class="icon-wrapper">
      <ServerCrash size={48} />
    </div>

    <h1>Orchestrateur indisponible</h1>
    <p class="message">
      Le service est temporairement indisponible. Nos équipes travaillent à rétablir le service dans les plus brefs délais.
    </p>

    <div class="error-details">
      <p class="error-message">{errorInfo().message}</p>
      <div class="error-code-wrapper">
        <span class="error-label">Code :</span>
        <code class="error-code">{errorInfo().code}</code>
      </div>
    </div>

    <div class="actions">
      <button class="retry-button" onclick={() => window.location.reload()}>
        <RefreshCw size={18} />
        <span>Réessayer</span>
      </button>

      <a href="/" class="back-link">Retour à l'accueil</a>
    </div>
  </div>
</div>

<style>
  .unavailable-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 2rem;
    background-color: var(--bg-primary);
  }

  .unavailable-card {
    max-width: 600px;
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
    background: rgba(239, 68, 68, 0.1);
    border-radius: 50%;
    color: #ef4444;
    margin-bottom: 1.5rem;
  }

  h1 {
    margin: 0 0 1rem;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
  }

  .message {
    margin: 0 0 2rem;
    font-size: 1rem;
    color: var(--text-secondary);
    line-height: 1.6;
  }

  .error-details {
    margin: 0 0 2rem;
    padding: 1.25rem;
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    text-align: center;
  }

  .error-message {
    margin: 0 0 1rem;
    font-size: 0.9375rem;
    color: var(--text-primary);
    line-height: 1.5;
  }

  .error-code-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .error-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .error-code {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #ef4444;
    letter-spacing: 0.5px;
  }

  .actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 0;
  }

  .retry-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9375rem;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
  }

  .retry-button:hover {
    background: var(--color-primary-dark);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
  }

  .retry-button:active {
    transform: translateY(0);
    box-shadow: var(--shadow-sm);
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.625rem 1.25rem;
    background: var(--bg-tertiary);
    color: var(--text-primary);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    transition: all 0.2s;
  }

  .back-link:hover {
    background: var(--bg-primary);
    border-color: var(--color-primary);
    color: var(--color-primary);
  }

  @media (max-width: 640px) {
    .unavailable-container {
      padding: 1rem;
    }

    .unavailable-card {
      padding: 2rem 1.5rem;
    }

    h1 {
      font-size: 1.5rem;
    }
  }
</style>
