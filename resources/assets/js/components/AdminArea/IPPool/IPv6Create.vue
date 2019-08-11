<template>
    <create
            model-internal-name="ipv6Pools"
            :model-name="$t('common.ipv6Pool')"
            :default-subnet-bits="128"
    ></create>
</template>

<script>
    import Create from "./Create";
    export default {
        name: "IPv6Create",
        components: {Create}
    }
</script>

<style scoped>

</style>