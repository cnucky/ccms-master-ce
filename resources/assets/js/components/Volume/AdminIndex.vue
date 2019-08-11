<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">卷</h1>
        </div>

        <admin-index-table :is-admin="true"></admin-index-table>
    </div>
</template>

<script>

    import AdminIndexTable from "./AdminIndexTable";
    export default {
        name: "AdminIndex",
        components: {AdminIndexTable}
    }
</script>

<style scoped>

</style>