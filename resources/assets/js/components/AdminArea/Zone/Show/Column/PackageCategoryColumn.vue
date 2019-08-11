<template>
    <td>{{ entry.category.name }}</td>
</template>

<script>
    export default {
        name: "PackageCategoryColumn",
        props: ["entry"],
    }
</script>

<style scoped>

</style>