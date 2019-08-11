<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">用户管理</h1>
        </div>

        <index-table></index-table>
    </div>
</template>

<script>
    import IndexTable from "./IndexTable";

    export default {
        name: "Index",
        components: {IndexTable},
    }
</script>

<style scoped>

</style>