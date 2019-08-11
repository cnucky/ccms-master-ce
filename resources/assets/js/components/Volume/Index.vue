<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">卷</h1>
        </div>

        <index-table :is-admin="false"></index-table>
    </div>
</template>

<script>
    import IndexTable from "./IndexTable";
    export default {
        name: "Volumes",
        components: {IndexTable},
    }
</script>

<style scoped>
</style>