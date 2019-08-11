<template>
    <compute-instance-user-column v-if="entry.trade" :entry="entry.trade" key-name="user"></compute-instance-user-column>
    <td v-else> - </td>
</template>

<script>
    import ComputeInstanceUserColumn from "../../ComputeInstance/Column/UserColumn";
    export default {
        name: "UserColumn",
        components: {ComputeInstanceUserColumn},
        props: ["entry", "keyName"],
    }
</script>

<style scoped>

</style>