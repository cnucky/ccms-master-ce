<template>
    <i-p-assignment-index-table model-internal-name="ipv4" model-name="IPv4" :user-data="userData"></i-p-assignment-index-table>
</template>

<script>
    import IPAssignmentIndexTable from "./IPAssignmentIndexTable";
    export default {
        name: "IPv4AssignmentIndex",
        components: {IPAssignmentIndexTable},
        props: ["userData"],
    }
</script>

<style scoped>

</style>