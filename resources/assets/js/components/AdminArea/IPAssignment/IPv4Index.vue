<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">
                IPv4分配管理
            </h1>
        </div>

        <div class="sixteen wide column">
            <index-table model-internal-name="ipv4" model-name="IPv4"></index-table>
        </div>
    </div>
</template>

<script>
    import IndexTable from "./IndexTable";
    export default {
        name: "IPv4Index",
        components: {IndexTable}
    }
</script>

<style scoped>

</style>