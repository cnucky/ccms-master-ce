<template>
    <i-p-assignment-index-table model-internal-name="ipv6" model-name="IPv6" :user-data="userData"></i-p-assignment-index-table>
</template>

<script>
    import IPAssignmentIndexTable from "./IPAssignmentIndexTable";
    export default {
        name: "IPv6AssignmentIndex",
        components: {IPAssignmentIndexTable},
        props: ["userData"],
    }
</script>

<style scoped>

</style>