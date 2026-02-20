<script>
  import { Zap, Shield, HeadphonesIcon, ArrowRight, Cloud, Server, Database } from 'lucide-svelte'
</script>

<div class="home">
  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-container">
      <div class="hero-content">
        <h1>
          <span class="brand">UnxWares</span>
          <span class="brand-cloud">Cloud</span>
        </h1>
        <p class="tagline">
          Infrastructure cloud performante et sécurisée pour votre entreprise
        </p>
        <div class="cta-buttons">
          <a href="/dashboard" class="btn btn-primary">
            <span>Accéder au Dashboard</span>
            <ArrowRight size={20} />
          </a>
        </div>
      </div>

      <div class="hero-visual">
        <div class="floating-card">
          <Cloud size={48} />
        </div>
        <div class="floating-card delay-1">
          <Server size={48} />
        </div>
        <div class="floating-card delay-2">
          <Database size={48} />
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <div class="container">
      <h2>Pourquoi choisir UnxWares ?</h2>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <Zap size={32} />
          </div>
          <h3>Performance optimale</h3>
          <p>Infrastructure haute performance pour des temps de réponse ultra-rapides</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <Shield size={32} />
          </div>
          <h3>Sécurité renforcée</h3>
          <p>Protection avancée de vos données avec chiffrement de bout en bout</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <HeadphonesIcon size={32} />
          </div>
          <h3>Support 24/7</h3>
          <p>Une équipe d'experts à votre disposition à tout moment</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <p>&copy; {new Date().getFullYear()} UnxWares. Tous droits réservés.</p>
    </div>
  </footer>
</div>

<style>
  .home {
    min-height: 100vh;
    background: var(--bg-primary);
  }

  /* Hero Section */
  .hero {
    position: relative;
    padding: 8rem 2rem 6rem;
    background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
    overflow: hidden;
  }

  .hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:
      radial-gradient(circle at 20% 50%, rgba(5, 12, 156, 0.1) 0%, transparent 50%),
      radial-gradient(circle at 80% 80%, rgba(5, 12, 156, 0.05) 0%, transparent 50%);
    pointer-events: none;
  }

  .hero-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    position: relative;
    z-index: 1;
  }

  .hero-content h1 {
    margin: 0 0 1.5rem;
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--text-primary);
  }

  .brand {
    display: block;
    color: var(--color-primary);
  }

  .brand-cloud {
    display: block;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .tagline {
    font-size: 1.25rem;
    color: var(--text-secondary);
    margin: 0 0 2.5rem;
    line-height: 1.6;
  }

  .cta-buttons {
    display: flex;
    gap: 1rem;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
  }

  .btn-primary {
    background: var(--color-primary);
    color: white;
    box-shadow: 0 4px 12px rgba(5, 12, 156, 0.3);
  }

  .btn-primary:hover {
    background: var(--color-primary-dark);
    box-shadow: 0 6px 20px rgba(5, 12, 156, 0.4);
    transform: translateY(-2px);
  }

  /* Hero Visual */
  .hero-visual {
    position: relative;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .floating-card {
    position: absolute;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    color: var(--color-primary);
    animation: float 6s ease-in-out infinite;
  }

  .floating-card:nth-child(1) {
    top: 20%;
    left: 10%;
  }

  .floating-card:nth-child(2) {
    top: 50%;
    right: 15%;
    animation-delay: -2s;
  }

  .floating-card:nth-child(3) {
    bottom: 15%;
    left: 25%;
    animation-delay: -4s;
  }

  @keyframes float {
    0%, 100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  /* Features Section */
  .features {
    padding: 6rem 2rem;
    background: var(--bg-primary);
  }

  .container {
    max-width: 1200px;
    margin: 0 auto;
  }

  .features h2 {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4rem;
  }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
  }

  .feature-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 2.5rem;
    transition: all 0.3s ease;
  }

  .feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--color-primary);
  }

  .feature-icon {
    display: inline-flex;
    padding: 1rem;
    background: var(--color-primary-bg);
    color: var(--color-primary);
    border-radius: 12px;
    margin-bottom: 1.5rem;
  }

  .feature-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.75rem;
  }

  .feature-card p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
    font-size: 0.9375rem;
  }

  /* Footer */
  .footer {
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-color);
    padding: 2rem;
    text-align: center;
  }

  .footer p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
  }

  /* Responsive */
  @media (max-width: 968px) {
    .hero-container {
      grid-template-columns: 1fr;
      gap: 3rem;
    }

    .hero-visual {
      height: 300px;
    }

    .hero-content h1 {
      font-size: 2.5rem;
    }

    .tagline {
      font-size: 1.125rem;
    }
  }

  @media (max-width: 640px) {
    .hero {
      padding: 4rem 1rem 3rem;
    }

    .hero-content h1 {
      font-size: 2rem;
    }

    .features {
      padding: 4rem 1rem;
    }

    .features h2 {
      font-size: 2rem;
    }

    .features-grid {
      grid-template-columns: 1fr;
    }

    .hero-visual {
      display: none;
    }
  }
</style>
