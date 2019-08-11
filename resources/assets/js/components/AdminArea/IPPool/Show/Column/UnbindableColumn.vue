<template>
    <td>
        <template v-if="entry.unbindable">是</template>
        <template v-else>否</template>
    </td>
</template>

<script>
    export default {
        name: "UnbindableColumn",
        props: ["entry"],
    }
</script>

<style scoped>

</style>