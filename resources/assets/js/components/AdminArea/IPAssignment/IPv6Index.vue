<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1 class="ui header">
                IPv6分配管理
            </h1>
        </div>

        <div class="sixteen wide column">
            <index-table model-internal-name="ipv6" model-name="IPv6"></index-table>
        </div>
    </div>
</template>

<script>
    import IndexTable from "./IndexTable";
    export default {
        name: "IPv6Index",
        components: {IndexTable}
    }
</script>

<style scoped>

</style>