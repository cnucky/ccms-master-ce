<template>
    <show
            model-internal-name="ipv4Pools"
            :model-name="$t('common.ipv4Pool')"
    ></show>
</template>

<script>
    import Show from "./Show";
    export default {
        name: "IPv4Show",
        components: {Show}
    }
</script>

<style scoped>

</style>