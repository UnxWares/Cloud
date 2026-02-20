<script>
    import { ChevronLeft, ChevronRight } from 'lucide-svelte'

    let { collapsed, handleCollapse } = $props()
</script>

<div class="sidebar-header" class:collapsed>
    <a href="/" class="logo">
        <img src="/resources/img/unxwares-icon.svg" alt="Logo UnxWares" class="logo-icon" />
        {#if !collapsed}
            <span class="logo-text">Dashboard</span>
        {/if}
    </a>

    <button class="collapse-btn" onclick={handleCollapse} aria-label={collapsed ? 'Déplier' : 'Replier'} type="button">
        {#if collapsed}
            <ChevronRight size={20} />
        {:else}
            <ChevronLeft size={20} />
        {/if}
    </button>
</div>

<style>
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-4);
        transition: all 0.3s ease;
        margin: var(--space-28) 0;
        flex-shrink: 0;
    }

    .sidebar-header.collapsed {
        flex-direction: column;
        gap: var(--space-12);
    }

    .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-4);
        text-decoration: none;
        color: var(--color-primary);
        font-weight: 700;
    }

    .logo-icon {
        height: 40px;
        width: auto;
        flex-shrink: 0;
    }

    .logo-text {
        font-size: 1.25rem;
        white-space: nowrap;
    }

    .collapse-btn {
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--space-8);
        border-radius: var(--space-8);
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .collapse-btn:hover {
        background-color: var(--bg-tertiary);
        color: var(--color-primary);
    }

    .collapse-btn:focus-visible {
        outline: 2px solid var(--color-primary);
        outline-offset: 2px;
    }
</style>
