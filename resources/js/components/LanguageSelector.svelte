<script>
  import { Globe } from 'lucide-svelte'
  import { locale, setLocale, availableLocales } from '../lib/i18n.js'

  let isOpen = $state(false)

  function selectLocale(localeCode) {
    setLocale(localeCode)
    isOpen = false
  }
</script>

<div class="language-selector">
  <button
    class="nav-item"
    onclick={() => isOpen = !isOpen}
    aria-label="Changer de langue"
    aria-expanded={isOpen}
  >
    <Globe size={20} aria-hidden="true" />
    <span class="label">{$locale.toUpperCase()}</span>
  </button>

  {#if isOpen}
    <div class="language-dropdown">
      {#each availableLocales as lang}
        <button
          class="language-option"
          class:active={$locale === lang.code}
          onclick={() => selectLocale(lang.code)}
        >
          {lang.name}
        </button>
      {/each}
    </div>
  {/if}
</div>

<style>
  .language-selector {
    position: relative;
  }

  .nav-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    background: transparent;
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    border: none;
    text-align: left;
  }

  .nav-item:hover {
    background: var(--bg-secondary);
    color: var(--text-primary);
  }

  .label {
    flex: 1;
  }

  .language-dropdown {
    position: absolute;
    bottom: calc(100% + 0.5rem);
    left: 0;
    right: 0;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    z-index: 100;
  }

  .language-option {
    width: 100%;
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: none;
    color: var(--text-primary);
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem;
    text-align: left;
    cursor: pointer;
    transition: background 0.2s;
  }

  .language-option:hover {
    background: var(--bg-secondary);
  }

  .language-option.active {
    background: var(--color-primary-bg);
    color: var(--color-primary);
    font-weight: 500;
  }

  @media (max-width: 768px) {
    .label {
      display: none;
    }

    .nav-item {
      justify-content: center;
      padding: 0.75rem;
    }
  }
</style>
