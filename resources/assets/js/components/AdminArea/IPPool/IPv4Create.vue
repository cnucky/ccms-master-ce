<template>
    <create
            model-internal-name="ipv4Pools"
            :model-name="$t('common.ipv4Pool')"
            :default-subnet-bits="32"
    ></create>
</template>

<script>
    import Create from "./Create";
    export default {
        name: "IPv4Create",
        components: {Create}
    }
</script>

<style scoped>

</style>