<script>
    import { Link, page } from '@inertiajs/svelte'

    let {
        collapsed,
        icon: IconComponent,
        path,
        label = 'Undefined',
        isChild = false,
    } = $props()

    let isActive = $derived($page.url === path)
    let showTooltip = $state(false)
    let itemElement = $state()
    let tooltipPosition = $state({ top: 0, left: 0 })

    function updateTooltipPosition() {
        if (itemElement) {
            const rect = itemElement.getBoundingClientRect()
            tooltipPosition = {
                top: rect.top + rect.height / 2,
                left: rect.right + 8,
            }
        }
    }

    function handleMouseEnter() {
        if (collapsed) {
            updateTooltipPosition()
            showTooltip = true
        }
    }
</script>

<li
    bind:this={itemElement}
    class="sidebar-item"
    class:collapsed
    class:child={isChild}
    onmouseenter={handleMouseEnter}
    onmouseleave={() => (showTooltip = false)}
>
    <Link href={path} class="sidebar-link {isActive ? 'active' : ''}">
        <IconComponent size={20} />
        {#if !collapsed}
            <span class="label">{label}</span>
        {/if}
    </Link>
    {#if showTooltip}
        <div
            class="tooltip"
            style:top="{tooltipPosition.top}px"
            style:left="{tooltipPosition.left}px"
        >
            {label}
        </div>
    {/if}
</li>

<style>
    .sidebar-item {
        text-decoration: none;
        list-style: none;
    }

    .sidebar-item.child:not(.collapsed) :global(.sidebar-link) {
        padding-left: 2.5rem;
        font-size: 0.875rem;
    }

    .tooltip {
        position: fixed;
        transform: translateY(-50%);
        padding: 0.5rem 0.75rem;
        background-color: var(--bg-primary);
        color: var(--text-primary);
        font-size: 0.875rem;
        border-radius: 4px;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        pointer-events: none;
        z-index: 300;
        animation: fadeIn 0.2s ease;
        border: 1px solid var(--border-color);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-50%) translateX(-4px); }
        to   { opacity: 1; transform: translateY(-50%) translateX(0); }
    }

    :global(.sidebar-link) {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 0.75rem 1.5rem;
        background-color: transparent;
        color: var(--text-secondary);
        font-size: 0.875rem;
        font-weight: 400;
        cursor: pointer;
        transition: background-color 0.2s ease;
        text-decoration: none;
        border: none;
        text-align: left;
    }

    :global(.sidebar-link > svg) {
        flex-shrink: 0;
    }

    :global(.sidebar-link:hover) {
        background-color: var(--bg-tertiary);
        color: var(--text-tertiary);
    }

    :global(.sidebar-link.active) {
        background-color: var(--bg-tertiary);
        color: var(--color-primary);
        font-weight: 500;
        border-left: 3px solid var(--color-primary);
    }

    :global(.collapsed .sidebar-link) {
        padding: 0.75rem 1.25rem;
    }

    :global(.collapsed .sidebar-link.active) {
        border-left: 5px solid var(--color-primary);
    }
</style>
