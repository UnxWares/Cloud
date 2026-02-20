<script>
    import Sidebar from './Sidebar/Sidebar.svelte'
    import Header from './Header.svelte'

    let { children, title = '', subtitle = '' } = $props()
</script>

<div class="layout">
    <Sidebar />
    <Header {title} {subtitle} />
    <main id="main-content">
        {@render children()}
    </main>
</div>

<style>
    .layout {
        display: grid;
        grid-template-areas:
            "sidebar header"
            "sidebar main";
        grid-template-columns: auto 1fr;
        grid-template-rows: auto 1fr;
        height: 100vh;
    }

    main {
        grid-area: main;
        overflow-y: auto;
        background-color: var(--bg-primary);
    }
</style>
