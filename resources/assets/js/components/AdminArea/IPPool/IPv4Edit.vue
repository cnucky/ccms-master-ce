<template>
    <edit
            model-internal-name="ipv4Pools"
            :model-name="$t('common.ipv4Pool')"
    >
    </edit>
</template>

<script>
    import Edit from "./Edit";
    export default {
        name: "IPv4Edit",
        components: {Edit}
    }
</script>

<style scoped>

</style>