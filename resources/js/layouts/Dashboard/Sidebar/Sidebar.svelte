<script>
    import SidebarHeader from './SidebarHeader.svelte'
    import SidebarDivider from './SidebarDivider.svelte'
    import SidebarFooter from './SidebarFooter.svelte'
    import SidebarList from './List/SidebarList.svelte'

    let collapsed = $state(false)

    function handleCollapse() {
        collapsed = !collapsed
    }
</script>

<aside class:collapsed aria-label="Sidebar">
    <SidebarHeader {collapsed} {handleCollapse} />
    <SidebarDivider />
    <SidebarList {collapsed} />
    <SidebarDivider />
    <SidebarFooter {collapsed} />
</aside>

<style>
    aside {
        display: flex;
        flex-direction: column;
        grid-area: sidebar;
        background-color: var(--bg-secondary);
        border-right: 1px solid var(--border-color);
        width: 240px;
        transition: width 0.3s ease;
        max-height: 100vh;
        min-height: 0;
    }

    aside.collapsed {
        width: 60px;
        align-items: center;
    }
</style>
