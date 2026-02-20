<script>
    import { Globe, Cloud, Palette, ChartNoAxesColumnIncreasing } from 'lucide-svelte'

    let { collapsed } = $props()
</script>

<div class="sidebar-footer" class:collapsed>
    <div class="links">
        <a href="https://unxwares.com" title="Site principal" target="_blank" rel="noopener"><Globe size={16} /></a>
        <a href="https://cloud.unxwares.com" title="Cloud" target="_blank" rel="noopener"><Cloud size={16} /></a>
        <a href="https://studio.unxwares.com" title="Studio" target="_blank" rel="noopener"><Palette size={16} /></a>
        <a href="https://status.unxwares.com" title="Status" target="_blank" rel="noopener"><ChartNoAxesColumnIncreasing size={16} /></a>
    </div>
</div>

<style>
    .sidebar-footer {
        margin: 0.5rem 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 0.25rem;
        flex-shrink: 0;
        text-align: center;
        font-size: 0.75rem;
        color: var(--text-tertiary);
        transition: all 0.3s ease;
    }

    .sidebar-footer.collapsed {
        gap: 0.75rem;
    }

    .links {
        display: flex;
        margin-top: 0.25rem;
        gap: 0.5rem;
        justify-content: center;
    }

    .sidebar-footer.collapsed .links {
        flex-direction: column;
    }

    .links a {
        color: var(--color-primary);
        transition: color 0.2s ease;
    }

    .links a:hover {
        color: var(--text-primary);
    }
</style>
