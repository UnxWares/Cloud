<script>
    import { LayoutDashboard, Package, Dot } from 'lucide-svelte'
    import * as LucideIcons from 'lucide-svelte'
    import { page } from '@inertiajs/svelte'
    import SidebarItem from './SidebarItem.svelte'
    import SidebarSection from './SidebarSection.svelte'
    import SidebarItemDropdown from './SidebarItemDropdown.svelte'

    let { collapsed } = $props()

    let servicePacks = $derived($page.props.servicePacks || [])

    function getIcon(iconName) {
        if (!iconName) return Package
        const pascalCase = iconName
            .split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join('')
        return LucideIcons[pascalCase] || Package
    }
</script>

<div class="sidebar-list" class:collapsed>
    <nav class="sidebar-nav" aria-label="Navigation principale">
        <ul class="nav-menu">
            <SidebarItem
                {collapsed}
                icon={LayoutDashboard}
                path="/dashboard"
                label="Tableau de bord"
            />

            <SidebarSection {collapsed} title="Services" />

            {#each servicePacks as pack (pack.id)}
                <SidebarItemDropdown
                    {collapsed}
                    icon={getIcon(pack.icon)}
                    label={pack.name}
                    items={(pack.services || []).map(s => ({
                        icon: Dot,
                        path: `/dashboard/${pack.slug || pack.id}/${s.slug || s.id}`,
                        label: s.name || s.slug,
                    }))}
                />
            {/each}
        </ul>
    </nav>
</div>

<style>
    .sidebar-list {
        display: flex;
        justify-content: flex-start;
        gap: 0.25rem;
        transition: all 0.3s ease;
        margin: 0.5rem 0;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        max-width: 100%;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .sidebar-list::-webkit-scrollbar {
        display: none;
    }

    .sidebar-list.collapsed {
        gap: 0.75rem;
    }

    .sidebar-nav {
        width: 100%;
    }

    .nav-menu {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        max-width: 100%;
    }
</style>
