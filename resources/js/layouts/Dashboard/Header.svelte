<script>
    import { Sun, Moon } from 'lucide-svelte'
    import { theme, toggleTheme } from '../../lib/theme.js'

    let { title = '', subtitle = '' } = $props()
</script>

<header>
    <div class="header-left">
        {#if title}
            <div class="page-title">
                <h1>{title}</h1>
                {#if subtitle}<p class="subtitle">{subtitle}</p>{/if}
            </div>
        {/if}
    </div>
    <div class="header-right">
        <button
            class="theme-btn"
            onclick={toggleTheme}
            aria-label={$theme === 'dark' ? 'Activer le mode clair' : 'Activer le mode sombre'}
            aria-pressed={$theme === 'dark'}
            type="button"
        >
            {#if $theme === 'dark'}
                <Sun size={20} />
            {:else}
                <Moon size={20} />
            {/if}
        </button>
    </div>
</header>

<style>
    header {
        grid-area: header;
        background-color: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
        box-sizing: border-box;
        padding: var(--space-26) var(--space-28);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title h1 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.3;
    }

    .page-title .subtitle {
        margin: 1px 0 0;
        font-size: 0.8125rem;
        color: var(--text-secondary);
    }

    .theme-btn {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        color: var(--text-secondary);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
    }

    .theme-btn:hover {
        color: var(--color-primary);
        background: var(--bg-tertiary);
    }

    .theme-btn:focus-visible {
        outline: 2px solid var(--color-primary);
        outline-offset: 2px;
    }
</style>
