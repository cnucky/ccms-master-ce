<template>
    <show
            model-internal-name="ipv6Pools"
            :model-name="$t('common.ipv6Pool')"
    ></show>
</template>

<script>
    import Show from "./Show";
    export default {
        name: "IPv6Show",
        components: {Show}
    }
</script>

<style scoped>

</style>