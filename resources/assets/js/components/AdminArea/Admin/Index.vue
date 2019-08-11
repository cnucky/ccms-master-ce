<template>
    <div class="ui grid">
        <div class="sixteen wide column">
            <h1>{{ $t('common.admin') }}</h1>
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
        components: {IndexTable},
    }
</script>

<style scoped>

</style>