<script>
    import { ChevronDown } from 'lucide-svelte'
    import SidebarItem from './SidebarItem.svelte'

    let { collapsed, icon: IconComponent, label, items } = $props()
    let isOpen = $state(false)
    let isDroppedDowned = $derived(isOpen || collapsed)

    function toggle() {
        isOpen = !isOpen
    }
</script>

<li class="sidebar-dropdown" class:collapsed>
    {#if !collapsed}
        <button
            class="sidebar-link dropdown-trigger"
            class:open={isOpen}
            onclick={toggle}
            type="button"
            aria-expanded={isOpen}
            aria-label="Toggle dropdown"
        >
            <IconComponent size={20} />
            <span class="label">{label}</span>
            <ChevronDown size={16} class="chevron {isOpen ? 'open' : ''}" />
        </button>
    {/if}

    <ul class="sidebar-dropdown-menu" class:open={isDroppedDowned}>
        {#each items as item (item.path)}
            <SidebarItem
                {collapsed}
                icon={item.icon}
                path={item.path}
                label={item.label}
                isChild={true}
            />
        {/each}
    </ul>
</li>

<style>
    .sidebar-dropdown {
        list-style: none;
    }

    :global(.dropdown-trigger) {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 0.75rem 1.5rem;
        background-color: transparent;
        color: var(--text-secondary);
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
        text-decoration: none;
        border: none;
        text-align: left;
        margin-bottom: 0.5rem;
        font-family: 'Poppins', sans-serif;
    }

    :global(.dropdown-trigger:hover) {
        background-color: var(--bg-tertiary);
        color: var(--text-tertiary);
    }

    :global(.dropdown-trigger .label) {
        flex: 1;
    }

    :global(.dropdown-trigger .chevron) {
        margin-left: auto;
        transition: transform 0.2s ease;
        transform-origin: center;
        transform: rotate(-90deg);
    }

    :global(.dropdown-trigger .chevron.open) {
        transform: rotate(0deg);
    }

    :global(.dropdown-trigger.open) {
        background-color: var(--bg-tertiary);
        color: var(--color-primary);
    }

    .sidebar-dropdown-menu {
        list-style: none;
        padding: 0;
        margin: 0 0 0.25rem 0;
        border-left: 2px solid var(--color-primary);
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition:
            max-height 0.3s ease-in-out,
            opacity 0.3s ease-in-out,
            margin 0.3s ease-in-out;
    }

    .sidebar-dropdown-menu.open {
        max-height: 100vh;
        opacity: 1;
    }
</style>
