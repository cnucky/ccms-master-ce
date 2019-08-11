<template>
    <local-volume-index-table :is-admin="true" :compute-node="computeNode"></local-volume-index-table>
</template>

<script>
    import LocalVolumeIndexTable from "./LocalVolumeIndexTable";
    export default {
        name: "LocalVolumeIndex",
        components: {LocalVolumeIndexTable},
        props: ["computeNode"],
    }
</script>

<style scoped>

</style>