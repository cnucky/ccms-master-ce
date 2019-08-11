<template>
    <template v-for="(value, key) in columns">
        <template v-if="typeof value === 'string'">
            <th @click="sortBy(key)"
                :class="[{ sorted: sortKey === key}, sortOrders[key] ? 'ascending' : 'descending' ]">{{ value }}</th>
        </template>
        <template v-else>
            <sortable-table-table-header :columns="value" :sort-orders="sortOrders" v-on:sort-by="sortBy"></sortable-table-table-header>
        </template>
    </template>
</template>

<script>
    export default {
        name: "SortableTableTableHeader",
        props: ["columns", "sortOrders"],
        methods: {
            sortBy: function (key) {
                this.$emit("sort-by", key);
            }
        }
    }
</script>

<style scoped>

</style>