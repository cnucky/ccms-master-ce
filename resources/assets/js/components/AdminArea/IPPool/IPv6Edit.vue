<template>
    <edit
            model-internal-name="ipv6Pools"
            :model-name="$t('common.ipv6Pool')"
    >
    </edit>
</template>

<script>
    import Edit from "./Edit";
    export default {
        name: "IPv6Edit",
        components: {Edit}
    }
</script>

<style scoped>

</style>