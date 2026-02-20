<script>
    let { collapsed, title } = $props()
</script>

{#if !collapsed}
    <h2>{title}</h2>
{:else}
    <div class="section-divider" role="separator" aria-hidden="true"></div>
{/if}

<style>
    h2 {
        margin-top: 1rem;
        font-size: 0.625rem;
        font-weight: 600;
        padding: 0.5rem 1.5rem;
        color: var(--text-secondary);
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .section-divider {
        width: 100%;
        height: 1px;
        background-color: transparent;
        margin: 1rem auto;
    }
</style>
