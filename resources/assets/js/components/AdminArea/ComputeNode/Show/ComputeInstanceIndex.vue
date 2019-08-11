<template>
    <compute-instance-index-table :compute-node="computeNode"></compute-instance-index-table>
</template>

<script>
    import ComputeInstanceIndexTable from "./ComputeInstanceIndexTable";
    export default {
        name: "ComputeInstanceIndex",
        components: {ComputeInstanceIndexTable},
        props: ["computeNode"],
    }
</script>

<style scoped>

</style>