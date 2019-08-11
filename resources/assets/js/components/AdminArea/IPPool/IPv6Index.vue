<template>
    <index
            model-internal-name="ipv6Pools"
            :model-name="$t('common.ipv6Pool')"
    >
    </index>
</template>

<script>

    import Index from "./Index";
    export default {
        name: "IPv6Index",
        components: {Index},
    }
</script>

<style scoped>

</style>