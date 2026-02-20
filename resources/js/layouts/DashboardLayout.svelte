<script>
    import Layout from './Dashboard/Layout.svelte'

    let { children, title = '', subtitle = '' } = $props()
</script>

<Layout {title} {subtitle}>
    {@render children()}
</Layout>
