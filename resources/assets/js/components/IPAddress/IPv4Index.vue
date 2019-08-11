<template>
    <index model-name="IPv4" model-route-name="publicIPv4Addresses" :is-v4="true"></index>
</template>

<script>
    import Index from "./Index";

    export default {
        name: "IPv4Index",
        components: {Index}
    }
</script>

<style scoped>

</style>