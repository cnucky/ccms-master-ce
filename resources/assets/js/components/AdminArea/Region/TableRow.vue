<template>
    <tr class="popup-table-row" :data-content="entry.description" data-position="top left">
        <slot></slot>
    </tr>
</template>

<script>
    export default {
        name: "TableRow",
        props: ["entry"],
    }
</script>

<style scoped>

</style>