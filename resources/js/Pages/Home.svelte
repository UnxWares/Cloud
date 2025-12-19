<script>
  let features = [
    {
      title: 'Performance',
      description: 'Des solutions optimisées pour une vitesse maximale',
      icon: '⚡'
    },
    {
      title: 'Sécurité',
      description: 'Protection avancée de vos données',
      icon: '🔒'
    },
    {
      title: 'Support',
      description: 'Une équipe dédiée à votre service',
      icon: '💬'
    }
  ]
</script>

<div class="home">
  <header class="hero">
    <div class="hero-content">
      <h1>UnxWares</h1>
      <p class="tagline">Solutions cloud innovantes pour votre entreprise</p>
      <div class="cta-buttons">
        <a href="/dashboard" class="btn btn-primary">Accéder au Dashboard</a>
        <a href="#features" class="btn btn-secondary">En savoir plus</a>
      </div>
    </div>
  </header>

  <section id="features" class="features">
    <h2>Nos fonctionnalités</h2>
    <div class="features-grid">
      {#each features as feature}
        <div class="feature-card">
          <div class="feature-icon">{feature.icon}</div>
          <h3>{feature.title}</h3>
          <p>{feature.description}</p>
        </div>
      {/each}
    </div>
  </section>

  <section class="about">
    <div class="about-content">
      <h2>À propos d'UnxWares</h2>
      <p>
        UnxWares propose des solutions cloud innovantes conçues pour répondre
        aux besoins des entreprises modernes. Notre plateforme offre performance,
        sécurité et simplicité d'utilisation.
      </p>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; 2024 UnxWares. Tous droits réservés.</p>
  </footer>
</div>

<style>
  .home {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  .hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8rem 2rem;
    text-align: center;
    flex: 0 0 auto;
  }

  .hero-content {
    max-width: 800px;
    margin: 0 auto;
  }

  h1 {
    font-size: 4rem;
    margin: 0 0 1rem 0;
    font-weight: 800;
    letter-spacing: -0.02em;
  }

  .tagline {
    font-size: 1.5rem;
    margin: 0 0 3rem 0;
    opacity: 0.95;
    font-weight: 300;
  }

  .cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
  }

  .btn {
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: transform 0.2s, box-shadow 0.2s;
    display: inline-block;
  }

  .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
  }

  .btn-primary {
    background: var(--bg-primary);
    color: #667eea;
  }

  .btn-secondary {
    background: rgba(255,255,255,0.2);
    color: white;
    border: 2px solid white;
  }

  .features {
    padding: 6rem 2rem;
    background: var(--bg-primary);
    flex: 1 0 auto;
  }

  .features h2 {
    text-align: center;
    font-size: 2.5rem;
    color: #333;
    margin: 0 0 4rem 0;
  }

  .features-grid {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
  }

  .feature-card {
    background: var(--bg-secondary);
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
  }

  .feature-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
  }

  .feature-card h3 {
    color: #333;
    font-size: 1.5rem;
    margin: 0 0 1rem 0;
  }

  .feature-card p {
    color: #666;
    line-height: 1.6;
    margin: 0;
  }

  .about {
    padding: 6rem 2rem;
    background: var(--bg-secondary);
    flex: 0 0 auto;
  }

  .about-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
  }

  .about h2 {
    font-size: 2.5rem;
    color: #333;
    margin: 0 0 2rem 0;
  }

  .about p {
    font-size: 1.2rem;
    color: #666;
    line-height: 1.8;
    margin: 0;
  }

  .footer {
    background: #333;
    color: white;
    text-align: center;
    padding: 2rem;
    flex: 0 0 auto;
  }

  .footer p {
    margin: 0;
    opacity: 0.8;
  }

  @media (max-width: 768px) {
    h1 {
      font-size: 2.5rem;
    }

    .tagline {
      font-size: 1.2rem;
    }

    .hero {
      padding: 4rem 1rem;
    }

    .features, .about {
      padding: 3rem 1rem;
    }

    .features h2, .about h2 {
      font-size: 2rem;
    }

    .features-grid {
      grid-template-columns: 1fr;
    }
  }

  :global(html) {
    margin: 0;
    padding: 0;
  }

  :global(body) {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
  }

  :global(*) {
    box-sizing: border-box;
  }
</style>
