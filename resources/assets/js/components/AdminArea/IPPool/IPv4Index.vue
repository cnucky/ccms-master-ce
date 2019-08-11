<template>
    <index
            model-internal-name="ipv4Pools"
            :model-name="$t('common.ipv4Pool')"
    >
    </index>
</template>

<script>

    import Index from "./Index";
    export default {
        name: "IPv4Index",
        components: {Index},
    }
</script>

<style scoped>

</style>