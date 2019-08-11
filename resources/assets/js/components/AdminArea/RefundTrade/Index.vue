<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">退款订单</h1>
        </div>
        <div class="sixteen wide column">
            <index-table></index-table>
        </div>
    </div>
</template>

<script>
    import IndexTable from "./IndexTable";
    export default {
        name: "Index",
        components: {IndexTable}
    }
</script>

<style scoped>

</style>