<script>
  import Dashboard from './components/Dashboard.svelte'
</script>

<Dashboard />

<style>
  :global(html) {
    margin: 0;
    padding: 0;
  }

  :global(body) {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    background: var(--bg-secondary);
    color: var(--text-primary);
    min-height: 100vh;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  :global(*) {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }
</style>
